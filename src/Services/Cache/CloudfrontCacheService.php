<?php

namespace A17\Twill\Services\Cache;

use Aws\CloudFront\CloudFrontClient;
use Aws\Result;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CloudfrontCacheService
{
    protected $client;

    /**
     * @var Config
     */
    protected $config;

    // Added for backwards compatibility. Should be removed in future releases.
    protected static $defaultRegion = 'us-east-1';

    protected static $defaultSdkVersion = '2016-01-13';

    /**
     * @return string
     */
    public static function getSdkVersion()
    {
        return config('services.cloudfront.sdk_version') ?? self::$defaultSdkVersion;
    }

    /**
     * @return string
     */
    public static function getRegion()
    {
        return config('services.cloudfront.region') ?? self::$defaultRegion;
    }

    /**
     * @return CloudFrontClient
     */
    public static function getClient()
    {
        return new CloudFrontClient([
            'region' => self::getRegion(),
            'version' => self::getSdkVersion(),
            'credentials' => [
                'key' => config('services.cloudfront.key'),
                'secret' => config('services.cloudfront.secret'),
            ],
        ]);
    }

    public function __construct(Config $config)
    {
        $this->config = $config;

        $client = static::getClient();
        if (is_object($client)) {
            $this->client = $client;
        }
    }

    /**
     * @param string[] $urls
     * @return void
     */
    public function invalidate($urls = ['/*'])
    {
        if (! $this->hasInProgressInvalidation()) {
            try {
                $this->createInvalidationRequest($urls);
            } catch (\Exception $exception) {
                Log::debug('Cloudfront invalidation request failed');
            }
        } else {
            Log::debug('Cloudfront : there are already some invalidations in progress');
        }
    }

    /**
     * @return bool
     */
    private function hasInProgressInvalidation()
    {
        $list = $this->client->listInvalidations(['DistributionId' => $this->config->get('services.cloudfront.distribution')])->get('InvalidationList');
        if (isset($list['Items']) && ! empty($list['Items'])) {
            return Collection::make($list['Items'])->where('Status', 'InProgress')->count() > 0;
        }

        return false;
    }

    private function createInvalidationRequest(array $paths = []): ?Result
    {
        if (is_object($this->client) && $paths !== []) {
            try {
                return $this->client->createInvalidation([
                    'DistributionId' => $this->config->get('services.cloudfront.distribution'),
                    'InvalidationBatch' => [
                        'Paths' => [
                            'Quantity' => count($paths),
                            'Items' => $paths,
                        ],
                        'CallerReference' => time(),
                    ],
                ]);
            } catch (\Exception) {
                Log::debug('Cloudfront invalidation request failed');
            }
        }

        return null;
    }
}

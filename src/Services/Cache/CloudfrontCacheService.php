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

    // Added for backwards compatibility. Should be removed in future releases.
    protected static string $defaultRegion = 'us-east-1';

    protected static string $defaultSdkVersion = '2016-01-13';

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

    public static function getClient(): \Aws\CloudFront\CloudFrontClient
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

    public function __construct(protected Config $config)
    {
        $client = static::getClient();
        if (is_object($client)) {
            $this->client = $client;
        }
    }

    /**
     * @param string[] $urls
     */
    public function invalidate(array $urls = ['/*']): void
    {
        if (! $this->hasInProgressInvalidation()) {
            try {
                $this->createInvalidationRequest($urls);
            } catch (\Exception) {
                Log::debug('Cloudfront invalidation request failed');
            }
        } else {
            Log::debug('Cloudfront : there are already some invalidations in progress');
        }
    }

    private function hasInProgressInvalidation(): bool
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

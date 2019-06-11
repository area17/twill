<?php

namespace A17\Twill\Services\Cache;

use Aws\CloudFront\CloudFrontClient;
use Log;

class CloudfrontCacheService
{
    protected $client = null;

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
        $cloudFront = new CloudFrontClient(array(
            'region' => self::getRegion(),
            'version' => self::getSdkVersion(),
            'credentials' => array(
                'key' => config('services.cloudfront.key'),
                'secret' => config('services.cloudfront.secret'),
            ),
        ));

        return $cloudFront;

    }

    public function __construct()
    {
        $client = static::getClient();
        if (is_object($client)) {
            $this->client = $client;
        }
    }

    /**
     * @param string[] $urls
     * @return void
     */
    public function invalidate($urls = ["/*"])
    {
        if (!$this->hasInProgressInvalidation()) {
            try {
                $this->createInvalidationRequest($urls);
            } catch (\Exception $e) {
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
        $list = $this->client->listInvalidations(array('DistributionId' => config('services.cloudfront.distribution')))->get('InvalidationList');
        if (isset($list['Items']) && !empty($list['Items'])) {
            return collect($list['Items'])->where('Status', 'InProgress')->count() > 0;
        }

        return false;
    }

    /**
     * @param array $paths
     * @return \Aws\Result
     */
    private function createInvalidationRequest($paths = array())
    {
        if (is_object($this->client) && count($paths) > 0) {
            try {
                $result = $this->client->createInvalidation(array(
                    'DistributionId' => config('services.cloudfront.distribution'),
                    'InvalidationBatch' => array(
                        'Paths' => array(
                            'Quantity' => count($paths),
                            'Items' => $paths,
                        ),
                        'CallerReference' => time(),
                    ),
                ));
            } catch (\Exception $e) {
                Log::debug('Cloudfront invalidation request failed');
            }

            return $result;
        }
    }
}

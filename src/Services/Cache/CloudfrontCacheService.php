<?php

namespace A17\CmsToolkit\Services\Cache;

use Aws\CloudFront\CloudFrontClient;
use Log;

class CloudfrontCacheService
{
    protected static $sdk_version = '2016-01-13';
    protected static $region = 'us-east-1';
    protected $client = null;

    public static function getSdkVersion()
    {
        return self::$sdk_version;
    }

    public static function getClient()
    {
        $cloudFront = new CloudFrontClient(array(
            'region' => self::$region,
            'version' => self::$sdk_version,
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

    private function hasInProgressInvalidation()
    {
        $list = $this->client->listInvalidations(array('DistributionId' => config('services.cloudfront.distribution')))->get('InvalidationList');
        if (isset($list['Items']) && !empty($list['Items'])) {
            return collect($list['Items'])->where('Status', 'InProgress')->count() > 0;
        }

        return false;

    }

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

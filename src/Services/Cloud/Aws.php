<?php

namespace A17\Twill\Services\Cloud;

use Aws\S3\S3Client;
use Illuminate\Support\Str;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;

class Aws
{
    public function filesystemFactory($source): Filesystem
    {
        $config = $this->getConfigFor($source);

        $client = new S3Client($config);

        $adapter = new AwsS3V3Adapter($client, $config['bucket'], $config['root']);

        return new Filesystem($adapter);
    }

    public function getConfigFor($disk): array
    {
        return [
            'credentials' => [
                'key' => $this->config($disk, 'key'),

                'secret' => $this->config($disk, 'secret'),
            ],

            'region' => $this->config($disk, 'region'),

            'root' => $this->config($disk, 'root', ''),

            'bucket' => $this->config($disk, 'bucket'),

            'version' => $this->config($disk, 'version', 'latest'),
        ];
    }

    public function config($disk, $key, $default = null)
    {
        $env1 = Str::upper(Str::snake($disk));

        $env2 = $env1 === 'AWS' ? 'S3' : 'AWS';

        $envSuffix = Str::upper($key);

        if (filled($value = config("filesystems.disks.{$disk}.{$key}"))) {
            return $value;
        }

        return env("{$env1}_{$envSuffix}", env("{$env2}_{$envSuffix}", $default));
    }
}

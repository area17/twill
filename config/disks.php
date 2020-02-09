<?php

$localRootPrefix = storage_path('app/public/');
$localUrlPrefix = request()->getScheme() . '://' . str_replace(['http://', 'https://'], '', env('APP_URL')) . '/storage/';

$mediaLocalConfig = [
    'driver' => 'local',
    'visibility' => 'public',
    'root' => $localRootPrefix . trim(config('twill.media_library.local_path'), '/ '),
    'url' => $localUrlPrefix . trim(config('twill.media_library.local_path'), '/ '),
];

$fileLocalConfig = [
    'driver' => 'local',
    'visibility' => 'public',
    'root' => $localRootPrefix . trim(config('twill.file_library.local_path'), '/ '),
    'url' => $localUrlPrefix . trim(config('twill.file_library.local_path'), '/ '),
];

$s3Config = [
    'driver' => 's3',
    'key' => env('S3_KEY', env('AWS_KEY', env('AWS_ACCESS_KEY_ID'))),
    'secret' => env('S3_SECRET', env('AWS_SECRET', env('AWS_SECRET_ACCESS_KEY'))),
    'region' => env('S3_REGION', env('AWS_REGION', env('AWS_DEFAULT_REGION', 'us-east-1'))),
    'bucket' => env('S3_BUCKET', env('AWS_BUCKET')),
    'root' => env('S3_ROOT', env('AWS_ROOT', '')),
    'use_https' => env('S3_UPLOADER_USE_HTTPS', env('S3_USE_HTTPS', env('AWS_USE_HTTPS', true))),
];

$azureConfig = [
    'driver' => 'azure',
    'key' => env('AZURE_ACCOUNT_KEY'),
    'name' => env('AZURE_ACCOUNT_NAME'),
    'container' => env('AZURE_CONTAINER', 'public'),
    'endpoint-suffix' => env('AZURE_ENDPOINT_SUFFIX', 'core.windows.net'),
    'use_https' => env('AZURE_UPLOADER_USE_HTTPS', env('AZURE_USE_HTTPS', true)),
];

$mediaConfigByEndpointType = [
    'local' => $mediaLocalConfig,
    's3' => $s3Config,
    'azure' => $azureConfig,
];

$fileConfigByEndpointType = [
    'local' => $fileLocalConfig,
    's3' => $s3Config,
    'azure' => $azureConfig,
];

return [
    'twill_media_library' => $mediaConfigByEndpointType[config('twill.media_library.endpoint_type')],
    'twill_file_library' => $fileConfigByEndpointType[config('twill.file_library.endpoint_type')],
];

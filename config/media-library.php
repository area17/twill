<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twill Media Library configuration
    |--------------------------------------------------------------------------
    |
    | This allows you to provide the package with your configuration
    | for the media library disk, endpoint type and others options depending
    | on your endpoint type.
    |
    | Supported endpoint types: 'local' and 's3'.
    | Set cascade_delete to true to delete files on the storage too when
    | deleting from the media library.
    | If using the 'local' endpoint type, define a 'local_path' to store files.
    | Supported image services:
    | - 'A17\CmsToolkit\Services\MediaLibrary\Imgix'
    | - 'A17\CmsToolkit\Services\MediaLibrary\Local'
    |
     */
    'disk' => 'libraries',
    'endpoint_type' => env('MEDIA_LIBRARY_ENDPOINT_TYPE', 's3'),
    'cascade_delete' => env('MEDIA_LIBRARY_CASCADE_DELETE', false),
    'local_path' => env('MEDIA_LIBRARY_LOCAL_PATH'),
    'image_service' => env('MEDIA_LIBRARY_IMAGE_SERVICE', 'A17\CmsToolkit\Services\MediaLibrary\Imgix'),
    'acl' => env('MEDIA_LIBRARY_ACL', 'private'),
    'filesize_limit' => env('MEDIA_LIBRARY_FILESIZE_LIMIT', 50),
    'allowed_extensions' => ['svg', 'jpg', 'gif', 'png', 'jpeg'],
];

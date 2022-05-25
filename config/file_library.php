<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Twill File Library configuration
    |--------------------------------------------------------------------------
    |
    | This allows you to provide the package with your configuration
    | for the file library disk, endpoint type and others options depending
    | on your endpoint type.
    |
    | Supported endpoint types: 'local' and 's3'.
    | Set cascade_delete to true to delete files on the storage too when
    | deleting from the file library.
    | If using the 'local' endpoint type, define a 'local_path' to store files.
    | Supported file services:
    | - 'A17\Twill\Services\FileLibrary\Disk'
    |
     */
    'disk' => 'twill_file_library',
    'endpoint_type' => env('FILE_LIBRARY_ENDPOINT_TYPE', 'local'),
    'cascade_delete' => env('FILE_LIBRARY_CASCADE_DELETE', false),
    'local_path' => env('FILE_LIBRARY_LOCAL_PATH', 'uploads'),
    'file_service' => env('FILE_LIBRARY_FILE_SERVICE', 'A17\Twill\Services\FileLibrary\Disk'),
    'acl' => env('FILE_LIBRARY_ACL', 'public-read'),
    'filesize_limit' => env('FILE_LIBRARY_FILESIZE_LIMIT', 50),
    'allowed_extensions' => [],
    'prefix_uuid_with_local_path' => false,
];

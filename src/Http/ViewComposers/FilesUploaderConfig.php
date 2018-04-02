<?php

namespace A17\CmsToolkit\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class FilesUploaderConfig
{
    public function compose(View $view)
    {
        $libraryDisk = config('cms-toolkit.file_library.disk');
        $endpointType = config('cms-toolkit.file_library.endpoint_type');
        $allowedExtensions = config('cms-toolkit.file_library.allowed_extensions');

        $filesUploaderConfig = [
            'endpointType' => $endpointType,
            'endpoint' => $endpointType === 'local' ? route('admin.file-library.files.store') : s3Endpoint($libraryDisk),
            'successEndpoint' => route('admin.file-library.files.store'),
            'signatureEndpoint' => route('admin.file-library.sign-s3-upload'),
            'endpointBucket' => config('filesystems.disks.' . $libraryDisk . '.bucket', 'none'),
            'endpointRegion' => config('filesystems.disks.' . $libraryDisk . '.region', 'none'),
            'accessKey' => config('filesystems.disks.' . $libraryDisk . '.key', 'none'),
            'csrfToken' => csrf_token(),
            'acl' => config('cms-toolkit.file_library.acl'),
            'filesizeLimit' => config('cms-toolkit.file_library.filesize_limit'),
            'allowedExtensions'=> $allowedExtensions
        ];

        $view->with(compact('filesUploaderConfig'));
    }
}

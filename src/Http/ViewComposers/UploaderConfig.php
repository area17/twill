<?php

namespace A17\CmsToolkit\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class UploaderConfig
{
    public function compose(View $view)
    {
        $libraryDisk = config('cms-toolkit.media_library.disk');
        $endpointType = config('cms-toolkit.media_library.endpoint_type');
        $allowedExtensions = config('cms-toolkit.media_library.allowed_extensions');

        $uploaderConfig = [
            'endpointType' => $endpointType,
            'endpoint' => $endpointType === 'local' ? route('admin.media-library.medias.store') : s3Endpoint($libraryDisk),
            'successEndpoint' => route('admin.media-library.medias.store'),
            'signatureEndpoint' => route('admin.media-library.sign-s3-upload'),
            'endpointRegion' => config('filesystems.disks.' . $libraryDisk . '.region', 'none'),
            'accessKey' => config('filesystems.disks.' . $libraryDisk . '.key', 'none'),
            'csrfToken' => csrf_token(),
            'acl' => config('cms-toolkit.media_library.acl'),
            'filesizeLimit' => config('cms-toolkit.media_library.filesize_limit'),
            'allowedExtensions'=> $allowedExtensions
        ];

        $view->with(compact('uploaderConfig'));
    }
}

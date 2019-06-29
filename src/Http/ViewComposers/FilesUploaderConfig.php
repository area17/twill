<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\UrlGenerator;

class FilesUploaderConfig
{
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function compose(View $view)
    {
        $libraryDisk = config('twill.file_library.disk');
        $endpointType = config('twill.file_library.endpoint_type');
        $allowedExtensions = config('twill.file_library.allowed_extensions');

        $filesUploaderConfig = [
            'endpointType' => $endpointType,
            'endpoint' => $endpointType === 'local' ? $this->urlGenerator->route('admin.file-library.files.store') : s3Endpoint($libraryDisk),
            'successEndpoint' => $this->urlGenerator->route('admin.file-library.files.store'),
            'signatureEndpoint' => $this->urlGenerator->route('admin.file-library.sign-s3-upload'),
            'endpointBucket' => config('filesystems.disks.' . $libraryDisk . '.bucket', 'none'),
            'endpointRegion' => config('filesystems.disks.' . $libraryDisk . '.region', 'none'),
            'endpointRoot' => config('filesystems.disks.' . $libraryDisk . '.root', ''),
            'accessKey' => config('filesystems.disks.' . $libraryDisk . '.key', 'none'),
            'csrfToken' => csrf_token(),
            'acl' => config('twill.file_library.acl'),
            'filesizeLimit' => config('twill.file_library.filesize_limit'),
            'allowedExtensions' => $allowedExtensions,
        ];

        $view->with(compact('filesUploaderConfig'));
    }
}

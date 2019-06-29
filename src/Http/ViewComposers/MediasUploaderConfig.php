<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Routing\UrlGenerator;

class MediasUploaderConfig
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

    /**
     * Binds data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $libraryDisk = config('twill.media_library.disk');
        $endpointType = config('twill.media_library.endpoint_type');
        $allowedExtensions = config('twill.media_library.allowed_extensions');

        $mediasUploaderConfig = [
            'endpointType' => $endpointType,
            'endpoint' => $endpointType === 'local' ? $this->urlGenerator->route('admin.media-library.medias.store') : s3Endpoint($libraryDisk),
            'successEndpoint' => $this->urlGenerator->route('admin.media-library.medias.store'),
            'signatureEndpoint' => $this->urlGenerator->route('admin.media-library.sign-s3-upload'),
            'endpointBucket' => config('filesystems.disks.' . $libraryDisk . '.bucket', 'none'),
            'endpointRegion' => config('filesystems.disks.' . $libraryDisk . '.region', 'none'),
            'endpointRoot' => config('filesystems.disks.' . $libraryDisk . '.root', ''),
            'accessKey' => config('filesystems.disks.' . $libraryDisk . '.key', 'none'),
            'csrfToken' => csrf_token(),
            'acl' => config('twill.media_library.acl'),
            'filesizeLimit' => config('twill.media_library.filesize_limit'),
            'allowedExtensions' => $allowedExtensions,
        ];

        $view->with(compact('mediasUploaderConfig'));
    }
}

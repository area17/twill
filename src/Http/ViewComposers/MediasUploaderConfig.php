<?php

namespace A17\Twill\Http\ViewComposers;

use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\UrlGenerator;

class MediasUploaderConfig
{
    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param UrlGenerator $urlGenerator
     * @param Config $config
     */
    public function __construct(UrlGenerator $urlGenerator, Config $config)
    {
        $this->urlGenerator = $urlGenerator;
        $this->config = $config;
    }

    public function compose(View $view)
    {
        $libraryDisk = $this->config->get('twill.media_library.disk');
        $endpointType = $this->config->get('twill.media_library.endpoint_type');
        $allowedExtensions = $this->config->get('twill.media_library.allowed_extensions');

        $mediasUploaderConfig = [
            'endpointType' => $endpointType,
            'endpoint' => $endpointType === 'local' ? $this->urlGenerator->route('admin.media-library.medias.store') : s3Endpoint($libraryDisk),
            'successEndpoint' => $this->urlGenerator->route('admin.media-library.medias.store'),
            'signatureEndpoint' => $this->urlGenerator->route('admin.media-library.sign-s3-upload'),
            'endpointBucket' => $this->config->get('filesystems.disks.' . $libraryDisk . '.bucket', 'none'),
            'endpointRegion' => $this->config->get('filesystems.disks.' . $libraryDisk . '.region', 'none'),
            'endpointRoot' => $this->config->get('filesystems.disks.' . $libraryDisk . '.root', ''),
            'accessKey' => $this->config->get('filesystems.disks.' . $libraryDisk . '.key', 'none'),
            'csrfToken' => csrf_token(),
            'acl' => $this->config->get('twill.media_library.acl'),
            'filesizeLimit' => $this->config->get('twill.media_library.filesize_limit'),
            'allowedExtensions' => $allowedExtensions,
        ];

        $view->with(compact('mediasUploaderConfig'));
    }
}

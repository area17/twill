<?php

namespace A17\Twill\Services\Uploader;

use DateTime;
use DateTimeZone;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;

class SignAzureUpload
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Config
     */
    protected $blobSharedAccessSignatureHelper;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getSasUrl(Request $request, SignUploadListener $listener, $disk = 'libraries')
    {
        try {
            $blobUri = $request->input('bloburi');
            $method = $request->input('_method');
            $permissions = '' ;
            if (strtolower($method) === 'put') {
                $permissions = 'w';
            } elseif (strtolower($method) === 'delete') {
                $permissions = 'd';
            }

            $this->blobSharedAccessSignatureHelper = new BlobSharedAccessSignatureHelper(
                $this->config->get('filesystems.disks.' . $disk . '.name'),
                $this->config->get('filesystems.disks.' . $disk . '.key')
            );

            $now = new DateTime("now", new DateTimeZone("UTC"));
            $expire = $now->modify('+15 min');

            $path = $this->config->get('filesystems.disks.' . $disk . '.container') . str_replace(azureEndpoint($disk), '', $blobUri);
            $sasUrl = $blobUri . '?' . $this->blobSharedAccessSignatureHelper->generateBlobServiceSharedAccessSignatureToken('b', $path, $permissions, $expire);
            return $listener->uploadIsSigned($sasUrl, false);
        } catch (\Exception $exception) {
            return $listener->uploadIsNotValid();
        }
    }
}

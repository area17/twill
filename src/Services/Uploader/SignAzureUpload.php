<?php

namespace A17\Twill\Services\Uploader;

use DateTime;
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

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    // TODO: create sas url for fineuploader
    public function getSasUrl(Request $request, SignAzureUploadListener $listener, $disk = 'libraries')
    {
        $blobUri = $request->input('bloburi');
        $method = $request->input('_method');

        $permissions = strtolower($method) === 'put'
            ? 'w'
            : strtolower($method) === 'delete'
                ? 'd'
                : '';

        $this->blobSharedAccessSignatureHelper = new BlobSharedAccessSignatureHelper(
            $this->config->get('filesystems.disks.' . $disk . '.account.name'),
            $this->config->get('filesystems.disks.' . $disk . '.account.key')
        );

        $now = new DateTime();
        $expire = $now->modify('+15 min');

        try {

            $sasUrl = $blobUri.'?'.$this->blobSharedAccessSignatureHelper->generateBlobServiceSharedAccessSignatureToken('b', $blobUri, $permissions, $expire);
//            ddd($blobUri,$sasUrl);
//            dd($sasUrl);
            return $listener->isValidSas($sasUrl);
        }
        catch (\Exception $e) {
            return $listener->isNotValidSas();
        }
    }
}

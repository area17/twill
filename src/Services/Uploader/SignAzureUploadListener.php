<?php


namespace A17\Twill\Services\Uploader;


interface SignAzureUploadListener
{
    public function isValidSas($sasUrl);

    public function isNotValidSas();
}

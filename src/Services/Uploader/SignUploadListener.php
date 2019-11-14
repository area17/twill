<?php

namespace A17\Twill\Services\Uploader;

interface SignUploadListener
{
    public function uploadIsSigned($signature, $isJsonResponse = true);

    public function uploadIsNotValid();
}

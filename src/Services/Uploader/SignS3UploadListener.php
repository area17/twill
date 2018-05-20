<?php

namespace A17\Twill\Services\Uploader;

interface SignS3UploadListener
{
    public function policyIsSigned($signedPolicy);

    public function policyIsNotValid();
}

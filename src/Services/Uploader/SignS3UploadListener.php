<?php

namespace Sb4yd3e\Twill\Services\Uploader;

interface SignS3UploadListener
{
    public function policyIsSigned($signedPolicy);

    public function policyIsNotValid();
}

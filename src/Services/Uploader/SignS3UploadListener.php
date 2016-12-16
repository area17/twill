<?php

namespace A17\CmsToolkit\Services\Uploader;

interface SignS3UploadListener
{
    public function policyIsSigned($signedPolicy);

    public function policyIsNotValid();
}

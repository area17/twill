<?php

namespace A17\Twill\Services\Cloud;

use Exception;

class Glide
{
    public function makeCloudSource($source = null)
    {
        if (blank($source)) {
            throw new Exception(
                'Glide source was not set, please set your GLIDE_SOURCE environment variable or pass the source.'
            );
        }

        if ($source === 's3' || $source === 'aws') {
            return app(Aws::class)->filesystemFactory($source);
        }

        if ($source === 'azure') {
            return app(Azure::class)->filesystemFactory($source);
        }

        return $source;
    }
}

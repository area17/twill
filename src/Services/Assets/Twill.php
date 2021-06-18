<?php

namespace A17\Twill\Services\Assets;

use Illuminate\Support\Facades\Cache;

class Twill
{
    function asset($file)
    {
        return $this->devAsset($file) ?? $this->twillAsset($file);
    }

    public function twillAsset($file)
    {
        $manifest = $this->readManifest();

        if (isset($manifest[$file])) {
            return $manifest[$file];
        }

        return '/' . config('twill.public_directory', 'twill') . '/' . $file;
    }

    public function getManifestFilename()
    {
        $fileName =
            public_path(config('twill.public_directory', 'twill')) .
            '/' .
            config('twill.manifest_file', 'twill-manifest.json');

        if (file_exists($fileName)) {
            return $fileName;
        }

        return base_path(
            'vendor/area17/twill/dist/assets/admin/twill-manifest.json'
        );
    }

    public function devAsset($file)
    {
        if (!$this->devMode()) {
            return null;
        }

        $devServerUrl = config('twill.dev_mode_url', 'http://localhost:8080');

        try {
            $manifest = $this->readJson(
                $devServerUrl .
                    '/' .
                    config('twill.manifest_file', 'twill-manifest.json')
            );
        } catch (\Exception $e) {
            throw new \Exception(
                'Twill dev assets manifest is missing. Make sure you are running the npm run serve command inside Twill.'
            );
        }

        return $devServerUrl . ($manifest[$file] ?? '/' . $file);
    }

    /**
     * @return mixed
     */
    private function readManifest()
    {
        try {
            return Cache::rememberForever('twill-manifest', function () {
                return $this->readJson($this->getManifestFilename());
            });
        } catch (\Exception $e) {
            throw new \Exception(
                'Twill assets manifest is missing. Make sure you published/updated Twill assets using the "php artisan twill:update" command.'
            );
        }
    }

    private function readJson($fileName)
    {
        return json_decode(file_get_contents($fileName), true);
    }

    private function devMode()
    {
        return app()->environment('local', 'development') &&
            config('twill.dev_mode', false);
    }
}

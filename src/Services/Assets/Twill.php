<?php

namespace A17\Twill\Services\Assets;

use Illuminate\Support\Facades\Cache;

class Twill
{
    public static $cache = [];

    public function asset($file)
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
            'vendor/area17/twill/dist/assets/twill/twill-manifest.json'
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
                // During dev mode and webpack 5 this is the valid path.
                'assets/twill/twill-manifest.json'
            );
        } catch (\Exception $exception) {
            throw new \Exception('Twill dev assets manifest is missing. Make sure you are running the npm run serve command inside Twill.', $exception->getCode(), $exception);
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
        } catch (\Exception $exception) {
            throw new \Exception('Twill assets manifest is missing. Make sure you published/updated Twill assets using the "php artisan twill:update" command.', $exception->getCode(), $exception);
        }
    }

    private function readJson($fileName)
    {
        $requestOptionsIgnoreSsl = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];

        if (self::$cache === []) {
            self::$cache = json_decode(
                file_get_contents($fileName, false, stream_context_create($requestOptionsIgnoreSsl)),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        return self::$cache;
    }

    private function devMode()
    {
        return app()->environment('local', 'development') &&
            config('twill.dev_mode', false);
    }
}

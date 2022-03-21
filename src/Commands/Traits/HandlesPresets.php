<?php

namespace A17\Twill\Commands\Traits;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * @method void publishConfig;
 */
trait HandlesPresets
{
    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter[]
     */
    protected $examplesStorage = [];

    /**
     * @var \Illuminate\Filesystem\FilesystemAdapter
     */
    protected $appRootStorage;

    protected function presetExists(string $preset): bool
    {
        return $this->getExamplesDirectoryStorage()->exists($preset);
    }

    protected function installPresetFiles(string $preset): void
    {
        $this->checkMeetsRequirementsForPreset($preset);

        // First publish the config as we overwrite it later.
        // @phpstan-ignore-next-line
        $this->publishConfig();

        $examplesStorage = $this->getExamplesStorage($preset);
        $appRootStorage = $this->getAppRootStorage();

        foreach ($examplesStorage->allDirectories() as $directory) {
            if ($appRootStorage->makeDirectory($directory)) {
                foreach ($examplesStorage->files($directory) as $file) {
                    $appRootStorage->put($file, $examplesStorage->get($file));
                }
            }
        }
    }

    /**
     * This method reverses the process. It goes over all the files in the example and copies them from the project to
     * the twill examples folder. If you have new files you need to manually copy them once.
     *
     * This is useful for developing examples as you can install it, update it, then copy it back.
     */
    protected function updatePreset(string $preset): void
    {
        $examplesStorage = $this->getExamplesStorage($preset);
        $appRootStorage = $this->getAppRootStorage();

        foreach ($examplesStorage->allDirectories() as $directory) {
            foreach ($examplesStorage->files($directory) as $file) {
                $examplesStorage->put($file, $appRootStorage->get($file));
            }
        }
    }

    private function getExamplesStorage(string $preset): FilesystemAdapter
    {
        if (! isset($this->examplesStorage[$preset])) {
            $this->examplesStorage[$preset] = Storage::build([
                'driver' => 'local',
                'root' => __DIR__ . '/../../../examples/' . $preset,
            ]);
        }

        return $this->examplesStorage[$preset];
    }

    private function getExamplesDirectoryStorage(): FilesystemAdapter
    {
        return Storage::build([
            'driver' => 'local',
            'root' => __DIR__ . '/../../../examples/',
        ]);
    }

    private function getAppRootStorage(): FilesystemAdapter
    {
        if (! $this->appRootStorage) {
            $this->appRootStorage = Storage::build([
                'driver' => 'local',
                'root' => base_path(),
            ]);
        }

        return $this->appRootStorage;
    }

    private function checkMeetsRequirementsForPreset(string $preset): void
    {
        if ($preset === 'blog') {
            if (! \Composer\InstalledVersions::isInstalled('kalnoy/nestedset')) {
                throw new \RuntimeException(
                    'Missing nestedset, please install it using "composer require kalnoy/nestedset"'
                );
            }
        }
    }
}

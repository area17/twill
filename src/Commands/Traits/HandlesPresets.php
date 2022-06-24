<?php

namespace A17\Twill\Commands\Traits;

/**
 * @method void publishConfig;
 */
trait HandlesPresets
{
    protected function presetExists(string $preset): bool
    {
        return file_exists($this->getExamplesStoragePath($preset)) && is_dir($this->getExamplesStoragePath($preset));
    }

    protected function installPresetFiles(string $preset, bool $fromTests = false): void
    {
        $this->checkMeetsRequirementsForPreset($preset);

        // First publish the config as we overwrite it later.
        // @phpstan-ignore-next-line
        if (!$fromTests) {
            $this->publishConfig();
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->getExamplesStoragePath($preset))
        );

        $files = [];

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }
            $files[] = [
                'from' => $file->getPathname(),
                'to' => str_replace(
                    $this->getExamplesStoragePath($preset),
                    $this->getAppRootPath(),
                    $file->getPathname()
                ),
            ];
        }

        $this->copyPresetFiles($files);
    }

    /**
     * This method reverses the process. It goes over all the files in the example and copies them from the project to
     * the twill examples folder. If you have new files you need to manually copy them once.
     *
     * This is useful for developing examples as you can install it, update it, then copy it back.
     */
    protected function updatePreset(string $preset): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->getExamplesStoragePath($preset))
        );

        $files = [];

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                continue;
            }
            $files[] = [
                'to' => $file->getPathname(),
                'from' => str_replace(
                    $this->getExamplesStoragePath($preset),
                    $this->getAppRootPath(),
                    $file->getPathname()
                ),
            ];
        }

        $this->copyPresetFiles($files);
    }

    private function copyPresetFiles(array $files): void
    {
        foreach ($files as $file) {
            $fileName = trim(substr($file['to'], strrpos($file['to'], DIRECTORY_SEPARATOR) + 1));
            $dir = str_replace($fileName, '', $file['to']);

            if (!file_exists($dir)) {
                if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                    throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
                }
            }

            copy($file['from'], $file['to']);
        }
    }

    private function getExamplesStoragePath(string $preset): string
    {
        return $this->getExamplesDirectoryPath() . DIRECTORY_SEPARATOR . $preset;
    }

    private function getExamplesDirectoryPath(): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'examples';
    }

    private function getAppRootPath(): string
    {
        return base_path();
    }

    private function checkMeetsRequirementsForPreset(string $preset): void
    {
        if ($preset === 'blog' && !\Composer\InstalledVersions::isInstalled('kalnoy/nestedset')) {
            throw new \RuntimeException(
                'Missing nestedset, please install it using "composer require kalnoy/nestedset"'
            );
        }
    }
}

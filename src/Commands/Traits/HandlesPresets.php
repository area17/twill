<?php

namespace A17\Twill\Commands\Traits;

use Illuminate\Support\Str;

/**
 * @method void publishConfig;
 */
trait HandlesPresets
{
    protected function presetExists(string $preset): bool
    {
        return file_exists($this->getExamplesStoragePath($preset)) && is_dir($this->getExamplesStoragePath($preset));
    }

    protected function generateExampleFromGit(string $preset): void
    {
        $basePath = base_path();
        $status = shell_exec('cd ' . $basePath . ' && git status --short');

        $targetDir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', '..', 'examples', $preset, '']);

        if (is_dir($targetDir)) {
            $this->rrmdir($targetDir);
        }

        $lines = explode(PHP_EOL, $status);

        $files = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line !== '') {
                [$action, $file] = explode(' ', $line);

                $files[] = [
                    'from' => base_path($file),
                    'to' => $targetDir . $file,
                ];
            }
        }

        $this->copyPresetFiles($files);
    }

    protected function installPresetFiles(string $preset, bool $fromTests = false, ?string $basePath = null): void
    {
        $this->checkMeetsRequirementsForPreset($preset);

        // First publish the config as we overwrite it later.
        if (!$fromTests) {
            // @phpstan-ignore-next-line
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
                    $basePath ?? $this->getAppRootPath(),
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

            if (str_ends_with($file['from'], DIRECTORY_SEPARATOR)) {
                $this->recurseCopy($file['from'], $file['to']);
            } else {
                copy($file['from'], $file['to']);
            }
        }
    }

    /**
     * @see https://www.php.net/manual/en/function.copy.php#91010
     */
    private function recurseCopy(string $from, string $to): void
    {
        $dir = opendir($from);
        if (!is_dir($to)) {
            mkdir($to);
        }
        while (false !== ($file = readdir($dir))) {
            if (($file !== '.') && ($file !== '..')) {
                if (is_dir($from . '/' . $file)) {
                    $this->recurseCopy($from . '/' . $file, $to . '/' . $file);
                } else {
                    copy($from . '/' . $file, $to . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @see https://www.php.net/manual/en/function.rmdir.php#98622
     */
    private function rrmdir($dir): void
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object !== "." && $object !== "..") {
                    if (filetype($dir . "/" . $object) === "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
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
        if ($preset === 'basic-page-builder' && class_exists('Kalnoy\Nestedset\NodeTrait')) {
            throw new \RuntimeException(
                'Missing nestedset, please install it using "composer require kalnoy/nestedset"'
            );
        }
    }
}

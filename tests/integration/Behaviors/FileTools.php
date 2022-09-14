<?php

namespace A17\Twill\Tests\Integration\Behaviors;

use Illuminate\Support\Str;

trait FileTools
{
    public function putContentToFilePath(string $content, $filePath): void {
        $requiredPath = Str::beforeLast($filePath, '/');

        $this->ensureDirectoryExists($requiredPath);

        file_put_contents(
            $filePath,
            $content
        );
    }
    public function copyFileFromTo(string $from, string $to): void
    {
        $requiredPath = Str::beforeLast($to, '/');

        $this->ensureDirectoryExists($requiredPath);

        copy($from, $to);
    }

    public function ensureDirectoryExists(string $path): void
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}

<?php

function stubs($dir = null): string
{
    return __DIR__ . '/stubs' . ($dir ? "/{$dir}" : '');
}

function clean_file($file): array|string|null
{
    return str_replace(
        [' ', "\n", "\r", "\t"],
        ['*', '', '', ''],
        preg_replace('!\s+!', ' ', $file)
    );
}

function rrmdir($dir): void
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object !== "." && $object !== "..") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && ! is_link($dir . "/" . $object)) {
                    rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        rmdir($dir);
    }
}

function cleanupTestState(string $basePath): void
{
    $toDelete = [
        'app/Providers/AppServiceProvider.php',
        'app/Http/Controllers/Twill',
        'app/Http/Requests/Twill',
        'app/Models',
        'app/Repositories',
        'app/Twill',
        'resources/views/twill',
        'resources/views/site',
        'database/migrations',
        'routes/twill.php',
        'config/twill.php',
        'config/twill-navigation.php',
    ];

    foreach ($toDelete as $path) {
        $path = $basePath . '/' . $path;
        if (is_dir($path)) {
            rrmdir($path);
        } elseif (file_exists($path)) {
            unlink($path);
        }
    }
}

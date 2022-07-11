<?php

function stubs($dir = null)
{
    return __DIR__ . '/stubs' . ($dir ? "/{$dir}" : '');
}

function read_file($path)
{
    return clean_file(file_get_contents($path));
}

function clean_file($file)
{
    return str_replace(
        [' ', "\n", "\r", "\t"],
        ['*', '', '', ''],
        preg_replace('!\s+!', ' ', $file)
    );
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object !== "." && $object !== "..") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object)) {
                    rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        rmdir($dir);
    }
}

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

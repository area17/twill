<?php

function stubs($dir)
{
    return __DIR__ . "/stubs/{$dir}";
}

function read_file($path)
{
    return str_replace(
        [' ', "\n", "\r", "\t"],
        ['', '', '', ''],
        file_get_contents($path)
    );
}

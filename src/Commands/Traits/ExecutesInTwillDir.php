<?php

namespace A17\Twill\Commands\Traits;

trait ExecutesInTwillDir
{
    public function executeInTwillDir(string $command): mixed
    {
        $twillDir = $this->getTwillDir();
        return shell_exec("cd $twillDir && $command");
    }

    public function getTwillDir(string $path = ''): string
    {
        return __DIR__ . '/../../../' . $path;
    }
}

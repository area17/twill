<?php

namespace A17\Twill\Commands;

use Illuminate\Console\Command as IlluminateCommand;
use Illuminate\Support\Str;

abstract class Command extends IlluminateCommand
{
    protected $exitCode = 0;

    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'display')) {
            $method = Str::camel(Str::after($method, 'display'));

            if ($method === 'error') {
                $this->exitCode = 1;
            }

            call_user_func_array([$this, $method], $parameters);
        }
    }

    /**
     * Executes the console command.
     */
    public function handle()
    {
        return $this->exitCode;
    }
}

<?php

namespace A17\Twill\Commands;

class SetupDevTools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:setup-devtools';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the twill dev tools for standards';

    /*
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->confirm('This command is only intended for a development environment and will change and create files in your project, do you want to continue?')) {
            $this->error('cancelled');

            return;
        }

        $basePath = base_path();
        $this->line('Installing php cs fixer');
        exec("cd $basePath && composer require friendsofphp/php-cs-fixer");

        $source = __DIR__ . '/../../.php-cs-fixer.dist.php';
        exec("cp $source $basePath/.php-cs-fixer.dist.php");

        $this->line('Installing prettier and eslint');
        exec(
            "cd $basePath && npm i --save-dev prettier eslint eslint-config-prettier"
        );

        $source = __DIR__ . '/../../.prettierrc.yml';
        exec("cp $source $basePath/.prettierrc.yml");

        $source = __DIR__ . '/../../.eslintrc.js';
        exec("cp $source $basePath/.eslintrc.js");
    }
}

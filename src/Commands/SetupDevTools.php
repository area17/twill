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
    public function handle(): void
    {
        if (! $this->confirm('This command is only intended for a development environment and will change and create files in your project, do you want to continue?')) {
            $this->error('cancelled');

            return;
        }

        $basePath = base_path();
        $this->line('Installing php cs fixer');
        exec(sprintf('cd %s && composer require friendsofphp/php-cs-fixer', $basePath));

        $source = __DIR__ . '/../../.php-cs-fixer.dist.php';
        exec(sprintf('cp %s %s/.php-cs-fixer.dist.php', $source, $basePath));

        $this->line('Installing prettier and eslint');
        exec(
            sprintf('cd %s && npm i --save-dev prettier eslint eslint-config-prettier', $basePath)
        );

        $source = __DIR__ . '/../../.prettierrc.yml';
        exec(sprintf('cp %s %s/.prettierrc.yml', $source, $basePath));

        $source = __DIR__ . '/../../.eslintrc.js';
        exec(sprintf('cp %s %s/.eslintrc.js', $source, $basePath));
    }
}

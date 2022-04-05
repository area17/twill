<?php

namespace A17\Twill\Commands;

use Illuminate\Support\Str;

class ModuleMakeDeprecated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:module {moduleName}
        {--B|hasBlocks}
        {--T|hasTranslation}
        {--S|hasSlug}
        {--M|hasMedias}
        {--F|hasFiles}
        {--P|hasPosition}
        {--R|hasRevisions}
        {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Twill Module (deprecated, use twill:make:module)';

    /**
     * Executes the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $options = collect($this->options());

        if (!$options['no-interaction']) {
            $options = $options->except('no-interaction');
        }

        $this->call('twill:make:module', [
            'moduleName' => $this->argument('moduleName'),
        ] + $options->mapWithKeys(function ($value, $key) {
            return ["--{$key}" => $value];
        })->toArray());
    }
}

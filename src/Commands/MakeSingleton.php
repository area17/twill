<?php

namespace A17\Twill\Commands;

class MakeSingleton extends ModuleMake
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:make:singleton {moduleName}
        {--B|hasBlocks}
        {--T|hasTranslation}
        {--S|hasSlug}
        {--M|hasMedias}
        {--F|hasFiles}
        {--R|hasRevisions}
        {--E|generatePreview}
        {--bladeForm}
        {--all}
        {--force}
        {--factory}
        {--packageDirectory=}
        {--packageNamespace=}
        {--parentModel=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Twill Singleton Module';

    /**
     * @var bool
     */
    protected $isSingleton = true;
}

<?php

namespace A17\Twill\Commands;

class MakeSingletonCapsule extends MakeCapsule
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:make:singleton-capsule {moduleName} {--packageDirectory=} {--packageNamespace=}
        {--B|hasBlocks}
        {--T|hasTranslation}
        {--S|hasSlug}
        {--M|hasMedias}
        {--F|hasFiles}
        {--P|hasPosition}
        {--R|hasRevisions}
        {--N|hasNesting}
        {--all}
        {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Twill Singleton capsule';

    /**
     * @var bool
     */
    protected $isSingleton = true;
}

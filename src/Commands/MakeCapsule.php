<?php

namespace A17\Twill\Commands;

class MakeCapsule extends ModuleMake
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:make:capsule {moduleName}
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

    protected $description = 'Create a new Twill Capsule';

    /**
     * @var bool
     */
    protected $isCapsule = true;
}

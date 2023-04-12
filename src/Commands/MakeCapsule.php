<?php

namespace A17\Twill\Commands;

class MakeCapsule extends ModuleMake
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twill:make:capsule {moduleName} {--singleton} {--packageDirectory=} {--packageNamespace=}
        {--B|hasBlocks}
        {--T|hasTranslation}
        {--S|hasSlug}
        {--M|hasMedias}
        {--F|hasFiles}
        {--P|hasPosition}
        {--R|hasRevisions}
        {--N|hasNesting}
        {--E|generatePreview}
        {--bladeForm}
        {--all}
        {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Twill Capsule';

    /**
     * @var null
     */
    protected $capsulesDirectory;

    /**
     * @var null
     */
    protected $namespace;

    /**
     * @var bool
     */
    protected $isCapsule = true;

    /**
     * @var bool
     */
    protected $isSingleton = false;

    public function handle(): void
    {
        $this->isSingleton = $this->hasOption('singleton') ? $this->option('singleton') : $this->isSingleton;

        parent::handle();
    }
}

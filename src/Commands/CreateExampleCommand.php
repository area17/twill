<?php

namespace A17\Twill\Commands;

use A17\Twill\Commands\Traits\HandlesPresets;
use Illuminate\Console\Command;

class CreateExampleCommand extends Command
{
    use HandlesPresets;

    protected $signature = 'twill:create-example {preset}';

    protected $description = 'Create an example based from the git status';

    public function handle(): void
    {
        $preset = $this->argument('preset');

        if ($this->presetExists($preset)) {
            if ($this->confirm('Preset exists, overwrite it?')) {
                $this->generateExampleFromGit($preset);
            } else {
                $this->warn('Cancelled.');
            }
        } else {
            $this->generateExampleFromGit($preset);
        }
    }
}

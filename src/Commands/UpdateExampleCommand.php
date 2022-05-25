<?php

namespace A17\Twill\Commands;

use A17\Twill\Commands\Traits\HandlesPresets;
use Illuminate\Console\Command;

class UpdateExampleCommand extends Command
{
    use HandlesPresets;

    protected $signature = 'twill:update-example {preset}';

    protected $description = 'Updates the twill examples folder';

    public function handle(): void
    {
        $preset = $this->argument('preset');

        if ($this->presetExists($preset)) {
            if ($this->confirm('Are you sure to update this preset?')) {
                $this->updatePreset($preset);
            } else {
                $this->warn('Cancelled.');
            }
        } else {
            $this->error("Could not find preset: $preset");
        }
    }
}

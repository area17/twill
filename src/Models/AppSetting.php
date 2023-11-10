<?php

namespace A17\Twill\Models;

use A17\Twill\Exceptions\Settings\SettingsDirectoryMissingException;
use A17\Twill\Facades\TwillAppSettings;
use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Services\Blocks\Block;
use A17\Twill\Services\Settings\SettingsGroup;
use Illuminate\Support\Str;
use SplFileInfo;

class AppSetting extends Model
{
    use HasBlocks;

    public $fillable = [
        'name',
        'published',
    ];

    protected $attributes = [
        'published' => true,
    ];

    private bool $didRegisterSettingsBlocks = false;

		// Custom function here for self-registering directories hmtwill
		public function getDirectories(): array
		{
			$directories = [];

			foreach(TwillAppSettings::getSettingsPaths() as $path)
			{
				$directory = base_path($path . $this->getSettingGroup()->getName());

				if(is_dir($directory)){
					$directories[] = $directory;
				}
			}

			if(count($directories) === 0){
				throw new SettingsDirectoryMissingException($directory);
			}

			return $directories;
		}

    /**
     * @return array|array<int,string>
     */
    public function getFormBlocks(): array
    {
        $finalList = [];
				foreach($this->getDirectories() as $directory) { // customized to iterate over directories
					foreach (scandir($directory) as $file) {
							if (str_starts_with($file, '.') || !str_ends_with($file, '.blade.php')) {
									continue;
							}

							$finalList[] = str_replace('.blade.php', '', $file);
					}
				}
        
        return $finalList;
    }

    public function getSettingGroup(): SettingsGroup
    {
        return TwillAppSettings::getGroupForName($this->name);
    }

    public function registerSettingBlocks(): void
    {
        if ($this->didRegisterSettingsBlocks) {
            return;
        }

        $moduleName = lcfirst(Str::plural(Str::afterLast(static::class, '\\')));

				foreach($this->getDirectories() as $directory){ // customized to iterate over directories 
					$blockCollection = TwillBlocks::getBlockCollection();

					foreach ($this->getFormBlocks() as $name) {
							$blockCollection->add(
									$block = Block::make(
											file: new SplFileInfo($directory . DIRECTORY_SEPARATOR . $name . '.blade.php'),
											type: Block::TYPE_SETTINGS,
											source: Block::SOURCE_CUSTOM
									)
							);
	
							$originalName = $block->name;
	
							$block->name = $moduleName . '.' . $this->getSettingGroup()->getName() . '.' . $originalName;
							$block->component = 'a17-block-' . $moduleName . '-' . $this->getSettingGroup()->getName() . '-' . $originalName;
					}
				}
    }
}

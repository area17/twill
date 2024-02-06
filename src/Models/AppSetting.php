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

    public static function booted()
    {
        self::saving(function (self $model) {
            /*
             * Here we remove the 'blocks' relation so that any developer hooking
             * into the saved event on the AppSetting can still fetch the settings
             * with the new values. The next time the setting facade is called to
             * retrieve a setting, the blocks relation is hydrated again.
             */
            $model->unsetRelation('blocks');
        });
    }

    /**
     * @return array|array<int,string>
     */
    public function getFormBlocks(): array
    {
        $directory = resource_path('views/twill/settings/' . $this->getSettingGroup()->getName());

        if (!is_dir($directory)) {
            throw new SettingsDirectoryMissingException($directory);
        }

        $finalList = [];
        foreach (scandir($directory) as $file) {
            if (str_starts_with($file, '.') || !str_ends_with($file, '.blade.php')) {
                continue;
            }

            $finalList[] = str_replace('.blade.php', '', $file);
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

        $directory = resource_path('views' . DIRECTORY_SEPARATOR . 'twill' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . $this->getSettingGroup()->getName());

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

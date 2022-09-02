<?php

namespace A17\Twill\Models;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Services\Blocks\Block;
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

    /**
     * @return array|array<int,string>
     */
    public function getFormBlocks(): array
    {
        $directory = resource_path('views/twill/settings/' . $this->getDirName());

        if (! is_dir($directory)) {
            throw new \Exception($directory . ' directory is expected to exist but could not be found.');
        }

        $finalList = [];
        foreach (scandir($directory) as $file) {
            if (str_starts_with($file, '.') || ! str_ends_with($file, '.blade.php')) {
                continue;
            }

            $finalList[] = str_replace('.blade.php', '', $file);
        }

        return $finalList;
    }

    public function getDirName(): string
    {
        return Str::slug($this->name);
    }

    public function registerSettingBlocks(): void
    {
        $moduleName = lcfirst(Str::plural(Str::afterLast(static::class, '\\')));

        $directory = resource_path('views' . DIRECTORY_SEPARATOR . 'twill' . DIRECTORY_SEPARATOR . 'settings' . DIRECTORY_SEPARATOR . $this->getDirName());

        $blockCollection = TwillBlocks::getBlockCollection();

        foreach (self::getFormBlocks() as $name) {
            $blockCollection->add(
                $block = Block::make(
                    file: new SplFileInfo($directory . DIRECTORY_SEPARATOR . $name . '.blade.php'),
                    type: Block::TYPE_SETTINGS,
                    source: Block::SOURCE_CUSTOM
                )
            );

            $originalName = $block->name;

            $block->name = $moduleName . '.' . $this->getDirName() . '.' . $originalName;
            $block->component = 'a17-block-' . $moduleName . '-' . $this->getDirName() . '-' . $originalName;
        }
    }
}

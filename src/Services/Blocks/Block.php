<?php

namespace A17\Twill\Services\Blocks;

use Illuminate\Support\Str;

class Block
{
    const SOURCE_APP = 'app';

    const SOURCE_TWILL = 'twill';

    const SOURCE_CUSTOM = 'custom';

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $trigger;

    /**
     * @var string
     */
    public $source;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $icon;

    /**
     * @var boolean
     */
    public $isNewFormat;

    /**
     * @var string
     */
    public $inferredType;

    /**
     * @var \Symfony\Component\Finder\SplFileInfo
     */
    public $file;

    /**
     * @var string
     */
    public $fileName;

    /**
     * @var string
     */
    public $contents;

    public function __construct($blockData = [])
    {
        $this->absorbData($blockData);
    }

    public function absorbData($data)
    {
        if (blank($data)) {
            return;
        }

        $this->title = $data['title'];
        $this->trigger = $data['trigger'];
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->icon = $data['icon'];
        $this->isNewFormat = $data['new_format'];
        $this->inferredType = $data['inferred_type'];
        $this->file = $data['file'];
        $this->contents = $data['contents'];

        $this->fileName = $this->file->getFilename();
    }

    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    public function list()
    {
        return collect([
            'title' => $this->title,
            'trigger' => $this->trigger,
            'name' => $this->name,
            'type' => $this->type,
            'icon' => $this->icon,
            'source' => $this->source,
            'new_format' => $this->isNewFormat ? 'yes' : '-',
            'file' => $this->file->getFilename(),
        ]);
    }

    public function makeName($name)
    {
        return Str::kebab($name);
    }

    public function legacyArray()
    {
        return [
            $this->name => [
                'title' => $this->title,
                'icon' => $this->icon,
                'component' => 'a17-block-' . $this->name,
            ],
        ];
    }
}

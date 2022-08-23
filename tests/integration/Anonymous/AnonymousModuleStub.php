<?php

use A17\Twill\Models\Behaviors\HasBlocks;
use A17\Twill\Models\Behaviors\HasTranslation;
use A17\Twill\Models\Model;

$modelClass = new class([], $this->fields, $this->namePlural, $this->modelTranslationClass, ) extends Model {
    use HasTranslation;
    use HasBlocks;

    public array $setProps = '#PROPS#';

    public $translationForeignKey;

    public $translationModel;

    public $table;

    public function __construct(array $attributes = [])
    {
        foreach ($this->setProps as $prop => $value) {
            if ($prop === 'translatedAttributes') {
                $value[] = 'active';
            }
            $this->{$prop} = $value;
        }
        parent::__construct($attributes);
    }
};

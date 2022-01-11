<?php

namespace A17\Twill\Helpers;

use A17\Twill\Models\Block;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

abstract class TwillBlock
{
    /**
     * @var array
     *
     * The validation rules for this block.
     */
    protected $rules = [];

    /**
     * @var array
     *
     * The validation rules for this block.
     */
    protected $rulesForTranslatedFields = [];

    public function __construct()
    {
    }

    public function getData(Block $block, array $data): array
    {
        return $data;
    }

    public function validate(array $formData, int $id): void
    {
        $finalValidator = Validator::make([], []);
        foreach ($this->rulesForTranslatedFields as $field => $rules) {
            foreach (config('translatable.locales') as $locale) {
                $data = $formData[$field][$locale] ?? null;
                $validator = Validator::make([$field => $data], [$field => $rules]);
                foreach ($validator->messages()->getMessages() as $key => $errors) {
                    foreach ($errors as $error) {
                        $finalValidator->getMessageBag()->add("blocks.$id" . "[$key][$locale]", $error);
                        $finalValidator->getMessageBag()->add("blocks.$locale", 'Failed');
                    }
                }
            }
        }
        foreach ($this->rules as $field => $rules) {
            $validator = Validator::make([$field => $formData[$field] ?? null], [$field => $rules]);
            foreach ($validator->messages()->getMessages() as $key => $errors) {
                foreach ($errors as $error) {
                    $finalValidator->getMessageBag()->add("blocks[$id][$key]", $error);
                }
            }
        }

        if ($finalValidator->errors()->isNotEmpty()) {
            throw new ValidationException($finalValidator);
        }
    }

    public static function getBlockClassForName(string $name): ?TwillBlock
    {
        $transformed = Str::studly($name) . 'Block';
        $className = "\App\Twill\Block\\$transformed";
        if (class_exists($className)) {
            return new $className();
        }

        return null;
    }

    public static function getBlockClassForView(string $view): ?TwillBlock
    {
        $exploded = explode('.', $view);

        return self::getBlockClassForName(array_pop($exploded));
    }
}

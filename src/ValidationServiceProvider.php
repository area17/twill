<?php

namespace A17\Twill;

use A17\Twill\Repositories\BlockRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Registers the package additional validation rules.
     */
    public function boot(): void
    {
        Validator::extend('absolute_or_relative_url', function ($attribute, $value, $parameters, $validator): bool {
            return Str::startsWith($value, '/') || Validator::make([$attribute => $value], [$attribute => 'url'])->passes();
        }, 'The :attribute should be a valid url (absolute or relative)');

        Validator::extend('relative_or_secure_url', function ($attribute, $value, $parameters): bool {
            return Str::startsWith($value, '/') || filter_var($value, FILTER_VALIDATE_URL) !== false && Str::startsWith($value, 'https');
        }, 'The :attribute should be a valid url (relative or https)');

        Validator::extend('web_color', function ($attribute, $value, $parameters, $validator): bool|int {
            return preg_match('#^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$#i', $value);
        });

        Validator::extend('phone_number', function ($attribute, $value, $parameters): bool|int {
            return preg_match("#^[+]?[0-9\-\ ]*$#", $value);
        });

        Validator::extend('validBlocks', function ($attribute, $value, $parameters, $validator): bool {
            $blockMessages = [];

            foreach ($value as $block) {
                $cmsBlock = $this->app->make(BlockRepository::class)->buildFromCmsArray($block, false);

                $rules = config('twill.block_editor.blocks.' . $cmsBlock['type'] . '.rules') ?? [];

                unset($cmsBlock['content']);

                $blockValidator = Validator::make(array_merge($block['content'], $cmsBlock), $rules);

                if (! $blockValidator->passes()) {
                    foreach ($blockValidator->errors()->all() as $error) {
                        $blockMessages[] = $error;
                    }
                }

                if (! empty($blockMessages)) {
                    array_unshift($blockMessages, 'This block has validation issues:');
                    $validator->errors()->add('block.' . $block['id'], implode('<br>', $blockMessages));
                }

                $blockMessages = [];
            }

            return true;
        });
    }

    public function register(): void
    {
    }
}

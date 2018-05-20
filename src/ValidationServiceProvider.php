<?php

namespace A17\Twill;

use Illuminate\Support\ServiceProvider;
use Validator;

class ValidationServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Validator::extend('absolute_or_relative_url', function ($attribute, $value, $parameters, $validator) {
            return starts_with($value, '/') || Validator::make([$attribute => $value], [$attribute => 'url'])->passes();
        }, 'The :attribute should be a valid url (absolute or relative)');

        Validator::extend('relative_or_secure_url', function ($attribute, $value, $parameters) {
            return starts_with($value, '/') || filter_var($value, FILTER_VALIDATE_URL) !== false && starts_with($value, 'https');
        }, 'The :attribute should be a valid url (relative or https)');

        Validator::extend('web_color', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i', $value);
        });

        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            return preg_match("/^[+]?[0-9\-\ ]*$/", $value);
        });
    }

    public function register()
    {

    }
}

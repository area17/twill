<?php

namespace A17\CmsToolkit;

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

        // $rules['medias.role.crop'] = 'media_dimensions:min_width=450,min_height=450';
        Validator::extend('media_dimensions', function ($attribute, $value, $parameters, $validator) {
            $parameters = array_reduce($parameters, function ($result, $item) {
                list($key, $value) = array_pad(explode('=', $item, 2), 2, null);
                $result[$key] = $value;
                return $result;
            });

            $width = $value['crop_w'][0];
            $height = $value['crop_h'][0];

            if (
                isset($parameters['width']) && $parameters['width'] != $width ||
                isset($parameters['min_width']) && $parameters['min_width'] > $width ||
                isset($parameters['max_width']) && $parameters['max_width'] < $width ||
                isset($parameters['height']) && $parameters['height'] != $height ||
                isset($parameters['min_height']) && $parameters['min_height'] > $height ||
                isset($parameters['max_height']) && $parameters['max_height'] < $height
            ) {
                return false;
            }

            if (isset($parameters['ratio'])) {
                list($numerator, $denominator) = array_pad(sscanf($parameters['ratio'], '%d/%d'), 2, 1);

                return $numerator / $denominator == $width / $height;
            }

            return true;
        });

        Validator::extend('phone_number', function ($attribute, $value, $parameters) {
            return preg_match("/^[+]?[0-9\-\ ]*$/", $value);
        });
    }

    public function register()
    {

    }
}

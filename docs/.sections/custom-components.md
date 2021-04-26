## Custom components

Twill supports custom Vue components to be used in your forms. To enable this feature make sure your `twill` configuration file has `custom_components_resource_path` set to the folder that contain your custom Vue components.
The path is relative to the Laravel resources folder. 

```
    return [
        ...
        'custom_components_resource_path' => 'js/components/admin',
        'js_namespace' => 'TWILL',
        ...
    ];
```

You have to run `php artisan twill:build` for your custom Vue components to be included in the frontend build.

To send the data to the server you need to store the variables to window.TWILL.STORE and window.TWILL.vm where `TWILL` represents the `twill.js_namespace` config. The default value of `js_namespace` is TWILL. For a more in depth tutorial, check out this [Spectrum post](https://spectrum.chat/twill/tips-and-tricks/adding-a-custom-block-to-twill-admin-view-with-vuejs~028d79b1-b3cd-4fb7-a89c-ce64af7be4af).

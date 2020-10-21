## Custom components

Twill supports custom Vue components to be used in your forms. To enable this feature make sure your `twill` configuration file has `custom_components_resource_path` set to the folder that contain your custom vue components.
The path is relative to the laravel resource folde. 

```
	return [
    ...
    'custom_components_resource_path' => 'js/components/admin',
    'js_namespace' => 'TWILL',
    ...
  ];
```

Next you will have to run `php artisan twill:build` for the changes to take effect.

To send the data to the server you need to store the variables to window.TWILL.STORE and window.TWILL.vm where `TWILL` reprisents the twill.js_namespace. 
The default value of `js_namespace` is TWILL.

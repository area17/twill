# Routes

A router facade is available to create module routes quicker:
```php
<?php

use A17\Twill\Facades\TwillRoutes;

TwillRoutes::module('yourModulePluralName');

// You can add an array of only/except action names as a second parameter
// By default, the following routes are created : 'reorder', 'publish', 'browser', 'bucket', 'feature', 'restore', 'bulkFeature', 'bulkPublish', 'bulkDelete', 'bulkRestore'
TwillRoutes::module('yourModulePluralName', ['except' => ['reorder', 'feature', 'bucket', 'browser']]);

// You can add an array of only/except action names for the resource controller as a third parameter
// By default, the following routes are created : 'index', 'store', 'show', 'edit', 'update', 'destroy'
TwillRoutes::module('yourModulePluralName', [], ['only' => ['index', 'edit', 'store', 'destroy']]);

// The last optional parameter disable the resource controller actions on the module
TwillRoutes::module('yourPluralModuleName', [], [], false);
```

---
pageClass: twill-doc
---

# Routes

A router macro is available to create module routes quicker:
```php
<?php

Route::twillModule('yourModulePluralName');

// You can add an array of only/except action names as a second parameter
// By default, the following routes are created : 'reorder', 'publish', 'browser', 'bucket', 'feature', 'restore', 'bulkFeature', 'bulkPublish', 'bulkDelete', 'bulkRestore'
Route::twillModule('yourModulePluralName', ['except' => ['reorder', 'feature', 'bucket', 'browser']]);

// You can add an array of only/except action names for the resource controller as a third parameter
// By default, the following routes are created : 'index', 'store', 'show', 'edit', 'update', 'destroy'
Route::twillModule('yourModulePluralName', [], ['only' => ['index', 'edit', 'store', 'destroy']]);

// The last optional parameter disable the resource controller actions on the module
Route::twillModule('yourPluralModuleName', [], [], false);
```

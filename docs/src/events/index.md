---
pageClass: twill-doc
---

# Events

Events are great way to alert your application that an action happened. Twill will notify you on most of the CMS activities.

Twill events are native Laravel events so check [documentation](https://laravel.com/docs/events) on how they work and how to use them.

## Events list
**Module events**
*   [Module create](./#module-create)
*   [Module delete](./#module-delete)
*   [Module destroy](./#module-destroy)
*   [Module duplicate](./#module-duplicate)
*   [Module feature](./#module-feature)
*   [Module publish](./#module-publish)
*   [Module reorder](./#module-reorder)
*   [Module restore](./#module-restore)
*   [Module update](./#module-update)

**Buckets event**
*   [Buckets save](./#buckets-save)

**File events**
*   [File stored](./#file-stored)
*   [File updated](./#file-updated)

**Media events**
*   [Media stored](./#media-stored)
*   [Media updated](./#media-updated)

### Handle events
To properly handle events you will need event listeners.

Event listeners are registered at `app\Providers\EventServiceProvider.php` in your local Laravel app.

Register example:
```php
// app\Providers\EventServiceProvider.php

protected $listen = [
    ExampleEvent::class => [
        ExampleListener::class,
    ],
];
```

Laravel listener example:
```php
class ExampleListener
{
    public function handle(ExampleEvent $event)
    {
        // Handle logic
    }
}
```

::: tip
Media and File are Twill modules and delete events can be cacthed via `ModuleDelete`. Module names are `files` and `medias`.
:::

### Module create

```
A17\Twill\Events\ModuleCreate
```

`ModuleCreate` event is fired on a new module record.

`module` - name of the module (string)  
`repository` - module model instance  


```php
public function handle(ModuleCreate $event)
{
    $event->module;
    $event->repository;
}
```

### Module delete

```
A17\Twill\Events\ModuleDelete
```

`ModuleDelete` event is fired on a soft delete of module record. It can be single or bulk delete. Soft deleted record can be restored in CMS.

`module` - name of the module (string)  
`repository` - module model instance  
`ids` - affected ids (array)  
`type` - `single` or `bulk`   

```php
public function handle(ModuleDelete $event)
{
    $event->module;
    $event->repository;
    $event->ids;
    $event->type;
}
```

### Module destroy

```
A17\Twill\Events\ModuleDestroy
```

`ModuleDestroy` event is fired on a hard delete of module record. It can be single or bulk. Destroyed record can't be restored.   

`module` - name of the module (string)  
`repository` - module model instance  
`ids` - affected ids (array)  
`type` - `single` or `bulk`   

```php
public function handle(ModuleDestroy $event)
{
    $event->module;
    $event->repository;
    $event->ids;
    $event->type;
}
```

### Module duplicate

```
A17\Twill\Events\ModuleDuplicate
```

`ModuleDuplicate` event is fired on a duplication of module record.   

`module` - name of the module (string)  
`repository` - module model instance  
`columnName` - index column (string)    

```php
public function handle(ModuleDuplicate $event)
{
    $event->module;
    $event->repository;
    $event->columnName;
}
```

### Module feature

```
A17\Twill\Events\ModuleFeature
```

`ModuleFeature` event is fired on featuring of module record. It can be single or bulk.

`module` - name of the module (string)  
`repository` - module model instance  
`ids` - affected ids (array)  
`type` - `single` or `bulk`   
`featured` - record featured or not (bool)       

```php
public function handle(ModuleFeature $event)
{
    $event->module;
    $event->repository;
    $event->ids;
    $event->type;
    $event->featured;
}
```

### Module publish

```
A17\Twill\Events\ModulePublish
```

`ModulePublish` event is fired on publishing of module record. It can be single or bulk.

`module` - name of the module (string)  
`repository` - module model instance  
`ids` - affected ids (array)  
`type` - `single` or `bulk`   
`published` - record published or not (bool)       

```php
public function handle(ModulePublish $event)
{
    $event->module;
    $event->repository;
    $event->ids;
    $event->type;
    $event->published;
}
```


### Module reorder

```
A17\Twill\Events\ModuleReorder
```

`ModuleReorder` event is fired on a new module records order change.

`module` - name of the module (string)  
`repository` - module model instance  


```php
public function handle(ModuleReorder $event)
{
    $event->module;
    $event->repository;
}
```


### Module restore

```
A17\Twill\Events\ModuleRestore
```

`ModuleRestore` event is fired on restoring soft deleted module record. It can be single or bulk delete.

`module` - name of the module (string)  
`repository` - module model instance  
`ids` - affected ids (array)  
`type` - `single` or `bulk`   

```php
public function handle(ModuleRestore $event)
{
    $event->module;
    $event->repository;
    $event->ids;
    $event->type;
}
```

### Module update

```
A17\Twill\Events\ModuleUpdate
```

`ModuleUpdate` event is fired on module update.

`module` - name of the module (string)  
`repository` - module model instance  


```php
public function handle(ModuleUpdate $event)
{
    $event->module;
    $event->repository;
}
```


### Buckets save

```
A17\Twill\Events\BucketSave
```

`BucketSave` event is fired on buckets save.

`buckets` - buckets saved (array)  
  


```php
public function handle(BucketSave $event)
{
    $event->buckets;
}
```

### File stored

```
A17\Twill\Events\FileStored
```

`FileStored` event is fired on file upload.

`repository` - module model instance    
  
```php
public function handle(FileStored $event)
{
    $event->repository;
}
```

### File updated

```
A17\Twill\Events\FileUpdated
```

`FileUpdated` event is fired on file update (tags).

`repository` - module model instance  
`ids` - affected ids (array)  
`tags` - tags submitted   
`type` - `single` or `bulk`    
  
```php
public function handle(FileUpdated $event)
{
    $event->repository;
    $event->ids;
    $event->tags;
    $event->type;
}
```

### Media stored

```
A17\Twill\Events\MediaStored
```

`MediaStored` event is fired on media upload.

`repository` - module model instance    
  
```php
public function handle(MediaStored $event)
{
    $event->repository;
}
```

### Media updated

```
A17\Twill\Events\MediaUpdated
```

`MediaUpdated` event is fired on media update (tags, altext, caption).

`repository` - module model instance  
`ids` - affected ids (array)  
`data` - data submitted   
`type` - `single` or `bulk`    
  
```php
public function handle(MediaUpdated $event)
{
    $event->repository;
    $event->ids;
    $event->data;
    $event->type;
}
```


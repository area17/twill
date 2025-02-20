# Settings

You can use Settings for various tasks such as: Global footer options, Shared blocks, Theme settings and much more.

In Twill 3.x settings were rebuild from the ground up to allow more fields and more flexible usage.

Settings exist out of the following:

- A settings group, this is usually also the menu entry
- Settings sections, these are a part of a group
- Settings, fields of any kind that are attached to sections.

Below we will go over the creation of a full setup.

## Building settings

### Settings group

Settings groups need to be registered so that Twill is aware of them.

Usually these groups are registered in your `AppServiceProvider`.

```php
use A17\Twill\Facades\TwillAppSettings;

public function boot(): void 
{
    TwillAppSettings::registerSettingsGroup(
        SettingsGroup::make()
            ->name('site-settings')
            ->label('Site settings')
            ->availableWhen(fn() => \Auth::user()->can('manage.settings')) // Example access control.
    );
}
```

Or you can register multiple at once:
```php
use A17\Twill\Facades\TwillAppSettings;

public function boot(): void 
{
    TwillAppSettings::registerSettingsGroups(
        SettingsGroup::make()
            ->name('site-settings')
            ->label('Site settings')
            ->availableWhen(fn() => \Auth::user()->can('manage.settings')) // Example access control.
        SettingsGroup::make()
          ...
    );
}
```

In the example above, we call the `TwillAppSettings` facade in which we register a new `SettingsGroup`

Notable methods for the settingsGroup are:

- `name(string)`: this is the machine name, and also the name of the folder and database that will be used later.
- `label(string)`: the label to use for this settings group, this will be used in menu entries overview page.
- `doNotAutoRegisterMenu()`: this can be used if you want to manually register the menu link. If not provided it will automatically be added under a top level **settings** menu entry in the primary navigation.
- `availableWhen(\Closure)`: A function that gives or removes access to the settings group. The closure should return true if the current user should be able to access it.

### Setting sections and fields

Once you have your settings group setup, you can go ahead and create sections. At this moment there is not yet a command line generator.

Based on the name used in the `SettingsGroup` we have to create a folder in `resources/views/twill/settings/{MyGroupName}` where `{MyGroupName}` is replaced with for example `site-settings`.

Inside these folders you can add **one** blade file for each of the settings sections. The blade file is identical to
that of a block, as internally blocks are used for making settings work.

We can add for example a file `copyright.blade.php` in order to hold our copyright settings:

```blade
@twillBlockTitle('Copyright')
@twillBlockIcon('text')
@twillBlockGroup('app')

<x-twill::wysiwyg name="left_text" label="Text in the left side" placeholder="Text" :translated="true" />
<x-twill::wysiwyg name="right_text" label="Text in the right side" placeholder="Text" :translated="true" />
<x-twill::input name="year" label="The year to use" placeholder="Text" />
```

This will make our settings group have a left text and a right text field.

## Accessing settings

While there is full flexibility as a settings group is a regular model behind the scenes, there are helpers for the most common actions.

For this we rely on the `TwillAppSettings` facade.

To access most, you need to know the identifier. To get that we have to combine the "group"."section"."field_name"

- `TwillAppSettings::getTranslated('site-settings.copyright.left_text')`: Get a translatable setting in the current locale.
- `TwillAppSettings::get('site-settings.copyright.year')`: Get a non-translatable setting.
- `TwillAppSettings::getGroupDataForSectionAndName('site-settings', 'copyright')`: Get the full `block` model for the settings.
- `TwillAppSettings::getBlockServiceForGroupAndSection('site-settings', 'copyright')`: Get the full block object, this is especially useful if you have a block editor in the settings, and you need to render those.

## Rendering nested block editors.

When you have a settings block that has an editor inside, you can render it by name like this:

```blade
{!! \TwillAppSettings::getBlockServiceForGroupAndSection('site-settings','text')->renderData->renderChildren('default') !!}
```

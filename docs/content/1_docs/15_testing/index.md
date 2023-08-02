# Testing Twill

Twill provides some dusk testing helpers to make it easy to get basic coverage of your cms's functionality.

## Setup Laravel dusk

### Installation

Depending on your setup, you can add Laravel dusk using `composer require --dev laravel/dusk`

Then you need to install the base files: `php artisan dusk:install`

You will also need to install Google Chrome and then install the correct browser driver:

`php artisan dusk:chrome-driver --detect`

Finally you can generate a first test using:

### Generating a test file

`php artisan dusk:make MyModuleTest`

### More

[See Laravel documentation for more details](https://laravel.com/docs/10.x/dusk)

## Authenticating

In order to login into the cms you need a user and login.

```php
$this->artisan('twill:superadmin user@example.org password');

$this->browse(function(Browser $browser) {
    $browser->loginAs(\App\Models\User::firstWhere('email', 'user@example.org'), 'twill_users');
});
```

## Dusk helpers

From within a dusk test you can run the following macro's:

**visitTwill**: Visit the admin panel.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();// [tl! focus]
});
```

**createModuleEntryWithTitle**: Create a specific module edit page.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();
    $browser->createModuleEntryWithTitle('Partners', 'Twill');// [tl! focus]
});
```

**visitModuleEntryWithTitle**: Visit a specific module edit page.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill');// [tl! focus]
});
```

**assertVselectHasOptions**: Check if a select field has given options.

The first parameter is the selector by which the field identifies.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill');
    $browser->assertVselectHasOptions('.input-wrapper-option', ['option1', 'option2']);// [tl! focus]
});
```

**selectVselectOption**: Select a given option.

The first parameter is the selector by which the field identifies.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill');
    $browser->selectVselectOption('.input-wrapper-option', 'option1');// [tl! focus]
});
```

**assertVselectHasOptionSelected**: Check if a given option is selected.

The first parameter is the selector by which the field identifies.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill');
    $browser->selectVselectOption('.input-wrapper-option', 'option1');
    $browser->assertVselectHasOptionSelected('.input-wrapper-option', 'option1');// [tl! focus]
});
```

**pressSaveAndCheckSaved**: Save and check that the save was successfully.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill');
    $browser->pressSaveAndCheckSaved();// [tl! focus]
});
```

**addBlockWithContent**: Add a new block and set the content.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill');

    $browser->addBlockWithContent('Text', [ // [tl! focus:start]
        'Title' => 'This is the title',
        'Text' => 'This is the wysiwyg'
    ]); // [tl! focus:end]

    $browser->pressSaveAndCheckSaved('Save as draft');
});
```

**withinNewBlock**: Same ass add block with content but for more manual control.

`$prefix` is the block identifier which is prefixed for block fields.

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill');

    $browser->withinNewBlock('Text', function(Browser $browser, string $prefix) { // [tl! focus:start]
        $browser->type($prefix . '[title][en]', 'Hello world');
    });// [tl! focus:end]

    $browser->pressSaveAndCheckSaved('Save as draft');
});
```

**withinEditor**: Opens the editor for the current page, allows you to interact and then closes it.

**dragBlock**: Allows you to drag a block in the editor. (When running in headed mode, this may not work as it uses your
cursor)

```php
$this->browse(function(Browser $browser) {
    $browser->visitTwill();

    $browser->createModuleEntryWithTitle('Page', 'Example page');

    $browser->withinEditor(function (Browser $editor) { // [tl! focus:start]
        $editor->dragBlock('Text', function (Browser $block, string $prefix) {
            $block->type($prefix . '[title][en]', 'Hello world');
        });
    }); // [tl! focus:end]

    $browser->pressSaveAndCheckSaved('Save as draft');
});
```

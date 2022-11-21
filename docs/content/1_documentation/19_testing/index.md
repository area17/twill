# Testing Twill

Twill provides some dusk testing helpers to make it easy to get basic coverage of your cms's functionality.

## Authenticating

In order to login into the cms you need a user and login.

```php
$this->browse(function(Browser $browser) {
    $browser->loginAs('ausername', 'twill_users');
});
```

## Dusk helpers

From within a dusk test you can run the following macro's:

**visitTwill**: Visit the admin panel.

```php
$this->browse(function(Browser $browser) {
    $browser->loginAs($this->superAdmin, 'twill_users');
    $browser->visitTwill();
});
```

**createModuleEntryWithTitle**: Create a specific module edit page.

```php
$this->browse(function(Browser $browser) {
    $browser->loginAs($this->superAdmin, 'twill_users');
    $browser->visitTwill();
    $browser->createModuleEntryWithTitle('Partners', 'Twill')
});
```

**visitModuleEntryWithTitle**: Visit a specific module edit page.

```php
$this->browse(function(Browser $browser) {
    $browser->loginAs($this->superAdmin, 'twill_users');
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill')
});
```

**assertVselectHasOptions**: Check if a select field has given options.

The first parameter is the selector by which the field identifies.

```php
$this->browse(function(Browser $browser) {
    $browser->loginAs($this->superAdmin, 'twill_users');
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill')
    $browser->assertVselectHasOptions('.input-wrapper-option', ['option1', 'option2']);
});
```

**selectVselectOption**: Select a given option.

The first parameter is the selector by which the field identifies.

```php
$this->browse(function(Browser $browser) {
    $browser->loginAs($this->superAdmin, 'twill_users');
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill')
    $browser->selectVselectOption('.input-wrapper-option', 'option1');
});
```

**assertVselectHasOptionSelected**: Check if a given option is selected.

The first parameter is the selector by which the field identifies.

```php
$this->browse(function(Browser $browser) {
    $browser->loginAs($this->superAdmin, 'twill_users');
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill')
    $browser->selectVselectOption('.input-wrapper-option', 'option1');
    $browser->assertVselectHasOptionSelected('.input-wrapper-option', 'option1')
});
```

**pressSaveAndCheckSaved**: Save and check that the save was successfull.

```php
$this->browse(function(Browser $browser) {
    $browser->loginAs($this->superAdmin, 'twill_users');
    $browser->visitTwill();
    $browser->visitModuleEntryWithTitle('Partners', 'Twill')
    $browser->pressSaveAndCheckSaved();
});
```

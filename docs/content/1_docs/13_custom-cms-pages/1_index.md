# Custom CMS Pages

Twill includes the ability to create fully custom pages that includes your navigation, by extending the `twill::layouts.free` layout in a view located in your `resources/views/admin` folder.

#### Example

- Create a route in `routes/twill.php`

```php
  Route::name('customPage')->get('/customPage', 'CustomPageController@show');
```

- Add a link to your page in `config/twill-navigation.php`

```php
return [
    ...
    'customPage' => [
        'title' => 'Custom page',
        'route' => 'admin.customPage',
    ],
    ...
];
```

- Add a controller to handle the request

```php
// file: app/Http/Controllers/Admin/CustomPageController.php

namespace App\Http\Controllers\Admin;

use A17\Twill\Http\Controllers\Admin\Controller;

class CustomPageController extends Controller
{
    public function show()
    {
        return view('admin.customPage');
    }
}
```

- And create the view

```php
// file: resources/views/admin/customPage.blade.php

@extends('twill::layouts.free')

@section('customPageContent')
  CUSTOM CONTENT GOES HERE
@stop
```

You can use Twill's Vue components if you need on those custom pages, for example:

```php
@extends('twill::layouts.free')

@section('customPageContent')
  <a17-fieldset>
    <a17-textfield name="input1" label="Text input"></a17-textfield>
    <a17-textfield name="input2" label="Text input with note" note="Side note"></a17-textfield>
    <a17-wysiwyg name="input3" label="WYSIWYG input with note" note="Side note"></a17-wysiwyg>
    <div class="wrapper">
      <div class="col--double col--double-wrap">
        <a17-wysiwyg name="input4" label="WYSIWYG input with note" note="Side note"></a17-wysiwyg>
      </div>
      <div class="col--double col--double-wrap">
        <a17-wysiwyg name="input5" label="WYSIWYG input with note" note="Side note"></a17-wysiwyg>
      </div>
    </div>
    <a17-inputframe label="Media field" note="Side note">
      <a17-mediafield name="input6"></a17-mediafield>
    </a17-inputframe>
    <a17-inputframe label="Browser field" note="Side note">
      <a17-browserfield name="input7" endpoint="/content/voices/browser"></a17-browserfield>
    </a17-inputframe>
  </a17-fieldset>
  <a17-button variant="validate" v-on:click="alert('from Twill Vue button');">Button variant: validate</a17-button>
@stop
```

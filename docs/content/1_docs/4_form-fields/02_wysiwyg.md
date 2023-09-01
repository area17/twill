# WYSIWYG

![screenshot](/assets/wysiwyg.png)

:::tabs=currenttab.FormBuilder&items.FormBuilder|FormView|Directive:::
:::tab=name.FormBuilder:::

```php
Wysiwyg::make()
    ->name('case_study')
    ->toolbarOptions([ [ 'header' => [1, 2, false] ], 'ordered', 'bullet' ])
    ->maxLength(200)
    ->note('Some note')
```

:::#tab:::
:::tab=name.FormView:::

```blade
<x-twill::wysiwyg 
    name="case_study" 
    :label="twillTrans('Case study text')"
    :placeholder="twillTrans('Case study placeholder')"
    :maxlength="200" 
    :note="twillTrans('Help text')"
/>

<x-twill::wysiwyg 
    name="case_study" 
    :label="twillTrans('Case study text')"
    :toolbar-options="[ [ 'header' => [1, 2, false] ], 'ordered', 'bullet' ]" 
    :placeholder="twillTrans('Case study placeholder')"
    :edit-source="true"
    :translated="true"
    :maxlength="200" 
    :note="twillTrans('Help text')"
/>
```

:::#tab:::
:::tab=name.Directive:::

```blade
@formField('wysiwyg', [
    'name' => 'case_study',
    'label' => 'Case study text',
    'placeholder' => 'Case study text',
    'maxlength' => 200,
    'note' => 'Hint message',
])

@formField('wysiwyg', [
    'name' => 'case_study',
    'label' => 'Case study text',
    'toolbarOptions' => [ [ 'header' => [1, 2, false] ] ],
    'placeholder' => 'Case study text',
    'maxlength' => 200,
    'editSource' => true,
    'note' => 'Hint message',
])
```

:::#tab:::
:::#tabs:::

By default, the WYSIWYG field is based on [Tiptap](https://tiptap.dev/).

You can configure the toolbar if needed.

For example, this configuration will render the *default* toolbar.

```blade
@php
$wysiwygOptions = [
    ['header' => [2, 3, 4, 5, 6, false]],
    'bold',
    'italic',
    'underline',
    'strike',
    'blockquote',
    "code-block",
    'ordered',
    'bullet',
    'hr',
    'code',
    'link',
    'clean',
    'table',
];
@endphp

<x-twill::wysiwyg 
    name="case_study" 
    :label="twillTrans('Case study text')"
    :toolbar-options="$wysiwygOptions"
    :placeholder="twillTrans('Case study placeholder')"
    :edit-source="true"
    :maxlength="200" 
    :note="twillTrans('Help text')"
/>
```


| Option         | Description                                                                                                              | Type/values         | Default value |
|:---------------|:-------------------------------------------------------------------------------------------------------------------------|:--------------------|:--------------|
| name           | Name of the field                                                                                                        | string              |               |
| label          | Label of the field                                                                                                       | string              |               |
| type           | Type of wysiwyg field                                                                                                    | quill<br/>tiptap    | tiptap        |
| toolbarOptions | Array of options/tools that will be displayed in the editor                                                              |                     | See above     |
| editSource     | Displays a button to view source code                                                                                    | boolean             | false         |
| hideCounter    | Hide the character counter displayed at the bottom                                                                       | boolean             | false         |
| limitHeight    | Limit the editor height from growing beyond the viewport                                                                 | boolean             | false         |
| translated     | Defines if the field is translatable                                                                                     | boolean             | false         |
| maxlength      | Max character count of the field                                                                                         | integer             |           |
| note           | Hint message displayed above the field                                                                                   | string              |               |
| placeholder    | Text displayed as a placeholder in the field                                                                             | string              |               |
| required       | Displays an indicator that this field is required<br/>A backend validation rule is required to prevent users from saving | boolean             | false         |
| direction      | Set custom input direction <small>(from `v3.1.0`)</small>                                                                | ltr<br/>rtl<br>auto | auto          |

Note that Quill outputs CSS classes in the HTML for certain toolbar modules (indent, font, align, etc.), and that the image module is not integrated with Twill's media library. It outputs the base64 representation of the uploaded image.
It is not a recommended way of using and storing images, prefer using one or multiple `medias` form fields or blocks fields for flexible content. This will give you greater control over your frontend output.

A migration to save a `wysiwyg` field would be:

```php
Schema::table('articles', function (Blueprint $table) {
    ...
    $table->text('case_study')->nullable();
    ...

});
// OR
Schema::table('article_translations', function (Blueprint $table) {
    ...
    $table->text('case_study')->nullable();
    ...
});
```

When used in a [block](../5_block-editor), no migration is needed.

## Advanced Tiptap usage

With the Tiptap wysiwyg editor you can access some additional features. Below is a more detailed explanation.

### Link browser

When needed, you can let users browse internal content, this can be especially useful to maintain correct links inside
wysiwyg editors.

This can currently only be done using the Form builder by adding the browsermodules to the
wysiwyg field:

```php
Wysiwyg::make()->name('description')
    ->label('Description')
    ->translatable()
    ->browserModules([Page::class])
```

Instead of taking the slug to the content during the time of writing the content, it let's you
render the url during render time.

When selecting a piece of content it will link it as such: `href="#twillInternalLink::App\Models\Page#1"`

This will then be replaced during render time if you are using the `$block->wysiwyg('field')` or `$block->translatedWysiwyg('field')` helpers.

You can customize the url by implementing the `getFullUrl` method on your model class.

For regular fields on models you will have to manually call `parseInternalLinks` like this:

```blade
{{ \A17\Twill\Facades\TwillUtil::parseInternalLinks($item->description) }}
```

## Manually setting input direction

Introduced in `v3.1.0`

For certain types of input it maybe useful to manually set the direction to left-to-right (`ltr`) or right-to-left (`rtl`) depending upon the expected text input; for example you may need a single Hebrew text entry in an otherwise `ltr` form. 

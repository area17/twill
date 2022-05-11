---
pageClass: twill-doc
---

# Tables

In Twill you can customize the module tables and browser tables to your own need.

To modify the table you start by overwriting the `getIndexTableColumns(): TableColumns` method.

You can manage this in 2 ways, but the easiest is to self define the columns without
calling `parent::getIndexTableColumns`.

[[toc]]

## Defining columns

To define columns start by instantiating a `TableColumns` object:

```php
use A17\Twill\Services\Listings\TableColumns;

protected function getIndexTableColumns(): TableColumns
{
    $columns = new TableColumns();
}
```

Then add as many fields as you need.

```php
use A17\Twill\Services\Listings\Columns\Text;
use A17\Twill\Services\Listings\TableColumns;

protected function getIndexTableColumns(): TableColumns
{
    $columns = new TableColumns();
    
    $columns->add(
        Text::make()
            ->field('title')
            ->title(__('Title'))
    );
    
    ...
}
```

## Column methods

There are currently 10 different columns. Most column share a set of setters that you can use. Because these columns
are regular php objects, you can always use your editor's autocomplete function to discover.

All columns are placed under the `src/Services/Listings/Columns` directory.

#### Frequently used

- `field(string $field)`: Sets the field in the model where the data should be fetched. This is usually mandatory but in
  some special columns it is not.
- `title(?string $title)`: The title to use, if none is provided a `Str::title` version of `field` will be used.
- `sortable(bool $sortable = true)`: Makes a column sortable.
- `optional(bool $optional = true)`: Makes a column optional.
- `hide(bool $hide = true)`: Hide the field by default. Only usable with optional.

#### Other methods

- `sortKey(?string $sortkey = null)`: The key to use when sorting.
- `renderHtml(bool $html = true)`: If the cms should render the contents as html. (Be careful when using this with
  unprotected data sources.)
- `customRender(Closure $renderFunction)`: A closure with a custom render function instead of using the raw field value.
- `linkCell(Closure|string $link)`: A closure or string on where to link the field contents to.

### CustomRender

CustomRender can be useful if you want more control over how you want to render a certain column.

The example blow illustrates a possible usage:

```php
Text::make()
  ->field('customField')
  ->customRender(function (Model $model) {
    return view('my.view', ['title' => $model->title'])->render();
  });
```

## Column types

Below is a list of columns and their function:

#### Text

`Text::make()->...`

Renders the (translated)value from the model

#### Boolean

`Boolean::make()->...`

Renders a ✅ or ❌ if the `field` is true or false.

#### Image

`Image:::make()->`

By default this renders the first role media in the model. You can specify the `role` and `crop` using their same named
methods.

If you add `rounded()` the image will be round.

Check the `Image` column class for more options.

#### PublishStatus

`PublishStatus:::make()->`

This field requires no additional methods, it shows on what dates the content will be published and when it will be
unpublished.

#### ScheduledStatus

`ScheduledStatus:::make()->`

This field requires no additional methods, it shows on what dates the content will be published and when it will be
unpublished.

#### NestedData

`NestedData::make()`

This field requires no additional methods, it shows information about the nested models.

#### Languages

`Languages::make()`

This field requires no additional methods, it will render the languages the content is available in.

#### Relation

`Relation::make()->...`

Renders the `field` of a  `relation` column.

For this column type both the relation and field should be provided. If one is missing an exception will be thrown.

#### Browser

`Browser::make()->...`

Renders the `field` of a  `browser` column.

For this column type both the browser and field should be provided. If one is missing an exception will be thrown.

#### Presenter

`Presenter::make()`

Renders a field using its presenter.

::: warning
Presenters are currently undocumented and are here for backward compatability. If you want to customize the output of
a column you can use the `customRender` method.
:::

## Custom columns

If you need you can easily define custom columns in your project by creating a class extending the `TableColumn`.

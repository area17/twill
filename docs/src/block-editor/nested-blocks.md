---
pageClass: twill-doc
---

# Nested blocks

Since Twill 3.x it is possible to nest blocks. In theory to infinity.

To make this work you can add **one** or **more** [block editors](form-fields/block-editor.html) to your block.

However, you have to specify their names

`resources/views/twill/blocks/nested-block.blade.php`

```php{11,12}
@twillBlockTitle('Nested Block left right')
@twillBlockIcon('text')
@twillBlockGroup('app')

@formField('input', [
    'name' => 'title',
    'label' => 'Title',
    'translated' => true,
])

@formField('block_editor', ['name' => 'left'])
@formField('block_editor', ['name' => 'right'])
```

With the example above we now have 2 editors. One is named `left` and the other `right`.

Now, let's move over to the rendering aspect.

## Basic usage

Following the documentation of [block editors](form-fields/block-editor.html) we know already how to render
a block.

`resources/views/site/blocks/nested-block.blade.php`

```php
@php
    /** @var \A17\Twill\Services\Blocks\RenderData $renderData */
@endphp
<div style="width: 100%">
    <div style="width: 50%; float: left;">
        Left
        {!! $renderData->renderChildren('left') !!}
    </div>
    <div style="width: 50%; float: left;">
        Right
        {!! $renderData->renderChildren('right') !!}
    </div>
</div>
```

`$renderData` is new in Twill 3.x, it is a nested representation of the data to be rendered.

## Manually rendering

By default the solution above will render all the children next to each other. But if you wish to wrap the
children each in their own container you can use the `getChildrenFor` method on the `$renderData`

```php
@php
    /** @var \A17\Twill\Services\Blocks\RenderData $renderData */
@endphp
<div style="width: 100%">
    <div style="width: 50%; float: left;">
        Left

        @foreach($renderData->getChildrenFor('left') as $leftBlock)
            <div style="background-color: green; padding: 150px;">
                {!! $leftBlock !!}
            </div>
        @endforeach
    </div>
    <div style="width: 50%; float: left;">
        Right

        @foreach($renderData->getChildrenFor('right') as $leftBlock)
            <div style="background-color: orange; padding: 150px;">
                {!! $leftBlock !!}
            </div>
        @endforeach
    </div>
</div>
```

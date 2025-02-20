# Nested blocks

Since Twill 3.x it is possible to nest blocks. In theory to infinity.

To make this work you can add **one** or **more** [block editors](../4_form-fields/block-editor.md) to your block.

However, you have to specify their names

:::filename:::
`resources/views/twill/blocks/nested-block.blade.php`
:::#filename:::

```blade
@twillBlockTitle('Nested Block left right')
@twillBlockIcon('text')
@twillBlockGroup('app')

<x-twill::input
    name="title"
    label="Title"
    :translated="true"
/>

<x-twill::block-editor name="left"/> <!-- [tl! focus] -->
<x-twill::block-editor name="right"/> <!-- [tl! focus] -->
```

With the example above we now have 2 editors. One is named `left` and the other `right`.

Now, let's move over to the rendering aspect.

## Basic usage

Following the documentation of [block editors](../4_form-fields/block-editor.md) we know already how to render
a block.

:::filename:::
`resources/views/site/blocks/nested-block.blade.php`
:::#filename:::

```blade
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

By default, the solution above will render all the children next to each other. But if you wish to wrap the
children each in their own container you can use the `getChildrenFor` method on the `$renderData`

```blade
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

        @foreach($renderData->getChildrenFor('right') as $rightBlock)
            <div style="background-color: orange; padding: 150px;">
                {!! $rightBlock !!}
            </div>
        @endforeach
    </div>
</div>
```

# Adding Repeater Fields to a Block

Inside a block, repeaters can be used too.

- Create a *container* block file, using a repeater form field:

:::filename:::
`views/twill/blocks/accordion.blade.php`
:::#filename:::

```blade
@twillBlockTitle('Accordion')
...
<x-twill::repeater type="accordion_item"/>
```

You can add other fields before or after your repeater, or even multiple repeaters to the same block.

- Create an *item* block, the one that will be repeated inside the *container* block

:::filename:::
`views/twill/repeaters/accordion_item.blade.php`
:::#filename:::

```blade
@twillRepeaterTitle('Accordion item')
@twillRepeaterMax('10') // Optional

<x-twill::input
    name="header"
    label="Header"
/>

<x-twill::input
    type="textarea"
    name="description"
    label="description"
    :rows="4"
/>
```

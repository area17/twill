# Custom Development Workflow

As of version 2.2, it is not necessary to rebuild Twill's frontend when working with blocks anymore. Their templates are now dynamically rendered in Blade and loaded at runtime by Vue. (For <2.1.x users, it means you do not need to run `php artisan twill:blocks` and `npm run twill-build` after creating or updating a block. Just reload the page to see your changes after saving your Blade file!)

This is possible because Twill's blocks Vue components are simple single file components that only have a template and a mixin registration. Blocks components are now dynamically registered by Vue using `x-template` scripts that are inlined by Blade.

#### Custom blocks and repeaters

To define a block as being `compiled` (i.e. using a custom Vue component), you can do this with the annotations `@twillPropCompiled('true')`, `@twillBlockCompiled('true')` or `@twillRepeaterCompiled('true')`. The imported Vue file will be preferred at runtime over the inline, template only, version. 

You can bootstrap your custom Vue blocks by generating them from their Blade counterpart using `php artisan twill:blocks`. It will ask you to confirm before overriding any existing custom Vue block. To start a custom Vue block from scratch, use the following template:

```vue

<template>
  <!-- eslint-disable -->
  <div class="block__body">
    <!-- CUSTOM CODE -->
  </div>
</template>

<script>
import BlockMixin from '@/mixins/block'

export default {
  mixins: [BlockMixin]
}
</script>

```

Note: For legacy 2.1.x users, in the `twill.block_editor.blocks` configuration array, set 'compiled' to `true` on the individual blocks.

If you are using custom Vue blocks (as in, you edited the `template`, `script` or `style` section of a generated block Vue file), you need to rebuild Twill assets.

There are two artisan commands to help you, and we recommend using them instead of our previous versions' npm scripts:

 - `php artisan twill:build`, which will build Twill's assets with your custom blocks, located in the `twill.block_editor.custom_vue_blocks_resource_path` new configurable path (with defaults to `assets/js/blocks`, like in previous versions).

 - `php artisan twill:dev`, which will start a local server that watches for changes in Twill's frontend directory. You need to set `'dev_mode' => true` in your `config/twill.php` file when using this command. This is especially helpful for Twill's contributors, but can also be useful if you use a lot of custom components in your application.

#### Naming convention of custom Vue components

The naming convention for custom blocks Vue component is deferred from the block's component name. For example, if your block's component name is `a17-block-quote`, the custom blocks should be `assets/js/blocks/BlockQuote.vue`. For component name with underscores, for example `a17-amazing_quote`, it would be `assets/js/blocks/BlockAmazing_quote.vue`.

#### Disabling inline blocks' templates

It is also possible to completely disable this feature by setting the `twill.block_editor.inline_blocks_templates` config flag to `false`.

If you do disable this feature, you could continue using previous versions' npm scripts, but we recommend you stop rebuilding Twill assets entirely unless you are using custom code in your generated Vue blocks. If you do keep using our npm scripts instead of our new Artisan commands, you will need to update `twill-build` from:

```
  "twill-build": "rm -f public/hot && npm run twill-copy-blocks && cd vendor/area17/twill && npm ci && npm run prod && cp -R public/* ${INIT_CWD}/public",
```

to:

```
  "twill-build": "npm run twill-copy-blocks && cd vendor/area17/twill && npm ci && npm run prod && cp -R dist/* ${INIT_CWD}/public",
```

#### A bit further: extending Twill with custom components and custom workflows

On top of custom Vue blocks, It is possible to rebuild Twill with custom Vue components. This can be used to override Twill's own Vue components or create new form fields, for example. The `twill.custom_components_resource_path` configuration can be used to provide a path under Laravel `resources` folder that will be used as a source of Vue components to include in your form js build when running `php artisan twill:build`.

You have to run `php artisan twill:build` for your custom Vue components to be included in the frontend build.

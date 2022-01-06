---
pageClass: twill-doc
---

# Previewing Blocks

At the top right of a form where you enabled a block editor, you will find a blue button labeled "Editor". The idea is to provide a better user experience when working with blocks, where the frontend preview is being immediately rendered next to the form, in a full-screen experience.

You can enable the content editor individual block previews by providing a `resources/views/site/layouts/block.blade.php` blade layout file. This file will be treated as a _layout_, so it will need to yield a `content` section: `@yield('content')`. It will also need to include any frontend CSS/JS necessary to give the block the look and feel of the corresponding frontend layout. Here's a simple example:

```php
<!doctype html>
<html>
    <head>
        <title>#madewithtwill website</title>
        <link rel="stylesheet" href="/css/app.css">
    </head>
    <body>
        <div>
            @yield('content')
        </div>
        <script src="/js/app.js"></script>
    </body>
</html>
```

If you would like to specify a custom layout view path, you can do so in `config/twill.php` at `twill.block_editor.block_single_layout`. A good way to share assets and structure from the frontend with these individual block previews is to create a parent layout and extend it from your block layout.

# Twill docs

## Prerequisites

You must already have installed node & php dependencies:
```
npm i
composer install
```

## View in browser

To compile and serve the docs site, run:
```
npm run docs
```

then go to `http://localhost:8000/docs`


## Other commands

To serve docs:
```
./vendor/bin/testbench twill:staticdocs:serve
```

To build docs:
```
./vendor/bin/testbench twill:staticdocs:generate
```

Or to build fresh (For example on layout/structure change)
```
./vendor/bin/testbench twill:staticdocs:generate --fresh
```

## FAQ

**Question:** I added/updated a new component in `docs/_templates/components` and it isn't showing updated code.

**Answer:** Templates are compiled in `docs/_build` and need to be refreshed if you make any component level changes, (classes, html, ect), however, this is not the case with `layout.blade.php`. You must stop the server, delete `updated.json` from `docs/_build/` and rerun the server. This will recompile the json file and you will see your changes.

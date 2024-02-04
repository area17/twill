# Twill docs

## Install

To compile and serve the docs site at `http://localhost:8000/` run at the ROOT of `/twill`
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

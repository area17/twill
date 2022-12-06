# Twill docs

## Install

Currently this requires a local system installation of tailwindcss

```
npm install -D -g tailwindcss
npm install -D -g @tailwindcss/typography
```

To run in development mode run in the root of the `/docs`
```
npm i
```

To compile and serve the docs site at `http://localhost:8000/` run at the ROOT of `/twill`
```
./vendor/bin/testbench twill:staticdocs:serve
```

To build docs:
```
./vendor/bin/testbench twill:staticdocs:build
```

Or to build fresh (For example on layout/structure change)
```
./vendor/bin/testbench twill:staticdocs:build --fresh
```

## FAQ

**Question:** I added/updated a new component in `docs/_templates/components` and it isnt showing updated code.

**Answer:** Templates are compiled in `docs/_build` and need to be refreshed if you make any component level changes, (classes, html, ect), however, this is not the case with `layout.blade.php`. You must stop the server, delete `updated.json` from `docs/_build/` and rerun the server. This will recompile the json file and you will see your changes

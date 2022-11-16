# Twill docs

## Install

Currently this requires a local system installation of tailwindcss

`
npm install -D -g tailwindcss
npm install -D -g @tailwindcss/typography
`

You can then serve the docs using:

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

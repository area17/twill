# Twill API docs

Uses [Doctum](https://github.com/code-lts/doctum) to generate a static website to browse the Twill API.

## Initial setup

```
cd docs-api
composer install
```

## Generate the documentation

```
composer run build
```

## Access the documentation locally

```
composer run serve
```

Visit [localhost:8000/2.x](http://localhost:8000/2.x/index.html) in your Web browser.

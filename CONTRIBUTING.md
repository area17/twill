# Code of Conduct
Twill is dedicated to building a welcoming, diverse, safe community. We expect everyone participating in the Twill community to abide by our [Code of Conduct](CODE_OF_CONDUCT.md). Please read it. Please follow it.

# Bug reports and features submission
To submit an issue or request a feature, please do so on [Github](https://github.com/area17/twill/issues).

If you file a bug report, your issue should contain a title and a clear description of the issue. You should also include as much relevant information as possible and a code sample that demonstrates the issue. The goal of a bug report is to make it easy for yourself - and others - to replicate the bug and develop a fix.

Remember, bug reports are created in the hope that others with the same problem will be able to collaborate with you on solving it. Do not expect that the bug report will automatically see any activity or that others will jump to fix it. Creating a bug report serves to help yourself and others start on the path of fixing the problem.

# Security vulnerabilities
If you discover a security vulnerability within Twill, please email us at [security@twill.io](mailto:security@twill.io). All security vulnerabilities will be promptly addressed.

# Versioning scheme

Twill follows [Semantic Versioning](https://semver.org/). Major releases are released only when breaking changes are necessary, while minor and patch releases may be released as often as every week. Minor and patch releases should never contain breaking changes.

When referencing Twill from your application, you should always use a version constraint such as `^3.0`, since major releases of Twill do include breaking changes.

# Which branch?
All bug fixes should be sent to the latest stable branch (`3.x`). Bug fixes should never be sent to the `main` branch unless they fix features that exist only in the upcoming release.

Minor features that are fully backwards compatible with the current Twill release may be sent to the latest stable branch (`3.x`).

Major new features should always be sent to the `main` branch, which contains the upcoming Twill release.

Please send coherent history — make sure each individual commit in your pull request is meaningful. If you had to make a lot of intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

# Coding style
- PHP: [PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).
- Javascript: [Standard](https://standardjs.com/), [Vue ESLint Essentials](https://github.com/vuejs/eslint-plugin-vue).

# Editor configuration tips

## Visual Studio Code

To make it easy to setup the correct configs when you are working in twill from a symlinked directory you can use the
command `php artisan twill:setup-devtools` to copy the configurations to your Laravel installation.

For JavaScript, Vue and SCSS formatting, the [Prettier](https://marketplace.visualstudio.com/items?itemName=esbenp.prettier-vscode) extension can do it for you. Install and set it as the default formatter in your `settings.json`.

```
"editor.formatOnSave": true,
"editor.defaultFormatter": "esbenp.prettier-vscode",
```

For PHP auto-formatting on save, you can use the [phpcsfixer](https://marketplace.visualstudio.com/items?itemName=junstyle.php-cs-fixer) extension and add to `settings.json`.

```
"php-cs-fixer.executablePath": "${workspaceRoot}/vendor/bin/php-cs-fixer",
"php-cs-fixer.formatHtml": true,
"php-cs-fixer.onsave": true,
"[php]": {
  "editor.defaultFormatter": "junstyle.php-cs-fixer"
},
```

For PHP contextual linting, you can use the [phpcs](https://marketplace.visualstudio.com/items?itemName=ikappas.phpcs) extension and then install [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) globally.

```
composer global require "squizlabs/php_codesniffer=*"
```

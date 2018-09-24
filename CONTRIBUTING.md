# Bug reports and features submission
To submit an issue or request a feature, please do so on [Github](https://github.com/area17/twill/issues).

If you file a bug report, your issue should contain a title and a clear description of the issue. You should also include as much relevant information as possible and a code sample that demonstrates the issue. The goal of a bug report is to make it easy for yourself - and others - to replicate the bug and develop a fix.

Remember, bug reports are created in the hope that others with the same problem will be able to collaborate with you on solving it. Do not expect that the bug report will automatically see any activity or that others will jump to fix it. Creating a bug report serves to help yourself and others start on the path of fixing the problem.

# Security vulnerabilities
If you discover a security vulnerability within Twill, please email us at [security@twill.io](mailto:security@twill.io). All security vulnerabilities will be promptly addressed.

# Versioning scheme
Twill's versioning scheme maintains the following convention: `paradigm.major.minor`. Minor releases should never contain breaking changes.

When referencing Twill from your application, you should always use a version constraint such as `1.2.*`, since major releases of Twill do include breaking changes.

The `VERSION` file at the root of the project needs to be updated and a Git tag created to properly release a new version.

# Which branch?
All bug fixes should be sent to the latest stable branch (1.2). Bug fixes should never be sent to the master branch unless they fix features that exist only in the upcoming release.

Minor features that are fully backwards compatible with the current Twill release may be sent to the latest stable branch (1.2).

Major new features should always be sent to the master branch, which contains the upcoming Twill release.

Please send coherent history — make sure each individual commit in your pull request is meaningful. If you had to make a lot of intermediate commits while developing, please [squash them](http://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

# Coding style
- PHP: [PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md).

- Javascript: [Standard](https://standardjs.com/), [Vue ESLint Essentials](https://github.com/vuejs/eslint-plugin-vue).
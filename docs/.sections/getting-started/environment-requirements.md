### Environment requirements
Twill is compatible with Laravel `5.3`, `5.4`, `5.5`, `5.6` and `5.7` applications running on PHP 7.

As a dependency to your own application, Twill shares Laravel's [server requirements](https://laravel.com/docs/5.6/installation#server-requirements), which are satisfied by both [Homestead](https://laravel.com/docs/5.6/homestead) and [Valet](https://laravel.com/docs/5.6/valet) during development, and easily deployed to production using [Forge](https://forge.laravel.com) and [Envoyer](https://envoyer.io) or [Envoy](https://laravel.com/docs/envoy), as well as any other Laravel compatible server configuration and deployment strategy.

Twill uses Laravel Mix to build the frontend assets of its UI. To ensure reproducible builds, npm scripts provided by Twill use the [npm `ci`](https://blog.npmjs.org/post/171556855892/introducing-npm-ci-for-faster-more-reliable) command, which is available since npm `5.7`.

Twill's database migrations create `json` columns. Your database should support the `json` type. Twill has been developed and tested against MySQL (`>=5.7`) and PostgreSQL(`>=9.3`).

In summary:

|            | Supported versions | Recommended version |
|:-----------|:------------------:|:-------------------:|
| PHP        | >= 7.0             | 7.2                 |
| Laravel    | >= 5.3             | 5.7                 |
| npm        | >= 5.7             | 6.4                 |
| MySQL      | >= 5.7             | 5.7                 |
| PostgreSQL | >= 9.3             | 10                  |

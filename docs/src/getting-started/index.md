---
pageClass: twill-doc
---

# Environment Requirements

Twill `2.x` is compatible with Laravel versions `5.8` to `8`, running on PHP 7.1 and above. As a dependency to your own application, Twill shares Laravel's [server requirements](https://laravel.com/docs/8.x/deployment#server-requirements).

## Development

For development, those requirements are satisfied by the following first-party solutions:

- [Sail](https://laravel.com/docs/8.x/sail) (All platforms)
- [Homestead](https://laravel.com/docs/8.x/homestead) (All platforms)
- [Valet](https://laravel.com/docs/8.x/valet) (macOS)

## Production

For production deployments, we recommend:

- [Forge](https://forge.laravel.com)
- [Envoyer](https://envoyer.io) or [Envoy](https://laravel.com/docs/8.x/envoy)

Of course, any other Laravel compatible server configuration and deployment strategy will be supported.

## Frontend assets

Twill uses [Vue CLI](https://cli.vuejs.org/) to build the frontend assets of its UI. To ensure reproducible builds, npm scripts provided by Twill use the [npm `ci`](https://blog.npmjs.org/post/171556855892/introducing-npm-ci-for-faster-more-reliable) command, which is available since npm `5.7`.

## Database

Twill's database migrations create `json` columns. Your database should support the `json` type. Twill has been developed and tested against MySQL (`>=5.7`) and PostgreSQL(`>=9.3`).

## Summary

|            | Supported versions | Recommended version |
|:-----------|:------------------:|:-------------------:|
| PHP        | >= 7.1             | 8.0                 |
| Laravel    | >= 5.8             | 9.x                 |
| npm        | >= 5.7             | 6.13                |
| MySQL      | >= 5.7             | 5.7                 |
| PostgreSQL | >= 9.3             | 10                  |


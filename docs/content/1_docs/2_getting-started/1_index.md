# Getting started

## Environment requirements

Twill `3.x` is compatible with Laravel versions above `9.x`, running on PHP `8.0` and above. As a dependency to your
own application, Twill shares Laravel's
[server requirements](https://laravel.com/docs/10.x/deployment#server-requirements).

## Development

For development, those requirements are satisfied by the following first-party solutions:

- [Sail](https://laravel.com/docs/10.x/sail) (All platforms)
- [Homestead](https://laravel.com/docs/10.x/homestead) (All platforms)
- [Valet](https://laravel.com/docs/10.x/valet) (macOS)

## Production

For production deployments, we can recommend:

- [Forge](https://forge.laravel.com)
- [Vapor](https://vapor.laravel.com)
- [Envoyer](https://envoyer.io)
- [Envoy](https://laravel.com/docs/10.x/envoy)
- [Deployer](https://deployer.org/)

Of course, any other Laravel compatible server configuration and deployment strategy will be supported.

## Frontend assets

Twill uses [Vue CLI](https://cli.vuejs.org/) to build the frontend assets of its UI. To ensure reproducible builds, npm
scripts provided by Twill use
the [npm](https://blog.npmjs.org/post/171556855892/introducing-npm-ci-for-faster-more-reliable) `ci` command, which is
available since npm `5.7`.

## Database

Twill's database migrations create `json` columns. Your database should support the `json` type. Twill has been
developed and tested against MySQL (`>=5.7`) and PostgreSQL(`>=9.3`) databases.

## Summary

|            | Supported versions | Recommended version |
|:-----------|:------------------:|:-------------------:|
| PHP        |       >= 8.0       |         8.2         |
| Laravel    |       >= 9.0       |        10.x         |
| Node.js    |       >= 16        |        18.x         |
| MySQL      |       >= 5.7       |         8.x         |
| PostgreSQL |       >= 9.3       |        15.x         |


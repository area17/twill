# Managing frontend user profiles from Twill

This is a simple solution to allow site administrators to manage
frontend user profiles from the CMS. It can be used as a starting point
to implement a user approval workflow and content access restrictions.

Objectives:

* Add a `Profiles` section to the CMS for site administrators
* Automatically create and assign `Profile` records to users upon
  registration

Requirements:

* [Laravel Breeze for user authentication](https://laravel.com/docs/10.x/starter-kits#laravel-breeze)

## Create the profiles module

This module will be used to attach extra information to `User` records:

```bash
php artisan twill:make:module Profiles
```

## Edit the migration

Update the generated migration to add the required fields:

:::filename:::
`database/migrations/2021_08_01_204153_create_profiles_tables.php`
:::#filename:::

```phptorch
{
  "file": "./manage_frontend_user_profiles_from_twill/2021_08_01_204153_create_profiles_tables.php",
  "focusMethods": "up"
}
```

Then, run the migration:

```bash
php artisan migrate
```

## Edit the Profile model

Edit the fillable fields to match the new schema and add the `Profile > User` relationship:

:::filename:::
`resources/views/admin/repeaters/tasks.blade.php`
:::#filename:::

```phptorch
{
  "file": "./manage_frontend_user_profiles_from_twill/Profile.php",
  "focusMethods": "user",
  "focusProperties": "fillable"
}
```

## Edit the User model

Define the `User > Profile` relationship and use the model's `booted()` method to
hook into the `created` event. When a user is created through the Laravel Breeze
user registration form, automatically create and assign a `Profile` record.

Finally, define the `name` attribute accessor. This will allow existing
Laravel Breeze code to access the user name from the attached profile:

:::filename:::
`app/Models/User.php`
:::#filename:::

```phptorch
{
  "file": "./manage_frontend_user_profiles_from_twill/User.php",
  "collapseAll": "",
  "focusMethods": ["profile", "booted", "getNameAttribute"]
}
```

## Edit ProfileController

Define our custom `name` column to be used instead of the default `title` and
prevent administrators from creating and deleting profiles in the CMS

:::filename:::
`app/Http/Controllers/Twill/ProfileController.php`
:::#filename:::

```phptorch
{
  "file": "./manage_frontend_user_profiles_from_twill/ProfileController.php",
  "collapseAll": "",
  "focusProperties": ["titleColumnKey", "indexOptions"]
}
```

## Edit the profile form

:::filename:::
`resources/views/twill/profiles/form.blade.php`
:::#filename:::

```blade
@extends('twill::layouts.form')

@section('contentFields')
    <x-twill::input
        type="textarea"
        name="description"
        label="Description"
        :maxlength="1000"
    />

    <x-twill::checkbox {{-- [tl! ++] --}}
        name="is_vip"{{-- [tl! ++] --}}
        label="Can access all VIP content"{{-- [tl! ++] --}}
    />
@stop
```

## Edit ProfileRequest

:::filename:::
`app/Http/Requests/Twill/ProfileRequest.php`
:::#filename:::

```phptorch
{
  "file": "./manage_frontend_user_profiles_from_twill/ProfileRequest.php",
  "collapseAll": "",
  "focusMethods": ["rulesForUpdate"]
}
```

## Finishing touches

Add the module to your `twill-navigation.php` and to your `admin.php`
routes and you are done!

## Where to go from here?

Site administrators now have access to a `Profiles` section in the CMS,
to edit basic user information.

Within your site's views and controllers, you can access the profile
information for the current user via `Auth::user()->profile`.

#### User approval & restricted content

Upon registration, a user profile is created with a `draft` status (ie.
not published). This can be used to implement a user approval workflow:

```blade
@if (Auth::user()->profile->published)
{{-- Account has been approved, show the dashboard --}}
@else
{{-- Account has not yet been approved, show "pending approval" message --}}
@endif
```

The same technique also applies for granular access control (e.g. a VIP
section with additional content).

#### Frontend profile editing

A frontend form can be added to allow users to edit their descriptions.
As always, make sure to sanitize user input before storing it in the
database.

#### Complete user management from the CMS

For simplicity, this implementation prevents site administrators from
creating and deleting users from the CMS.

A few methods from `ModuleRepository` can be extended in
`ProfileRepository` to implement the feature:

* [`afterSave()`](https://twillcms.com/docs/api/2.x/A17/Twill/Repositories/ModuleRepository.html#method_afterSave),
  for user creation
* [`afterDelete()`](https://twillcms.com/docs/api/2.x/A17/Twill/Repositories/ModuleRepository.html#method_afterDelete),
  for user deletion

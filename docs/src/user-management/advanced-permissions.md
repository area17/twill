---
pageClass: twill-doc
---

# Advanced permissions

As of Twill 3.x more advanced permissions are now available. These permissions can be setup from code and are managed
via the Twill admin interface.

## Setup

To enable permission management we have to add `permissions-management` to the `twill.enabled` configuration.

`config/twill.php`

```php
<?php

return [
  'enabled' => [
    'permissions-management'
  ]
];
```

In addition to this we have to configure the permissions system. There are 3 levels to choose from:

- **role**: this manages the access of modules based on the role of a user
- **roleGroup**: this manages the access of modules for a group of users, in addition to `role`
- **roleGroupItem**: this manages the permission of items within a module, in addition to `roleGroup`

Set the `twill.permissions.level` to the desired type. And also set the modules to manage in
the `twill.permissions.modules` key.

```php {7-10}
<?php

return [
  'enabled' => [
    'permissions-management'
  ],
  'permissions' => [
      'level' => 'role',
      'modules' => ['blogs'],
  ],
]
```

Once you have setup permission-management, make sure to run your database migrations:

```
php artisan migrate
```

## Level: Role

When using the permission level `role` users will be given a role.

Roles can be managed from the admin interface subnavigation when managing users.

The permission migration seeds 4 roles by default:

- **Owner**: Has permission to do anything on any module, but is not a superadmin.
- **Administrator**: Same as the **Owner** but cannot manage users and must be assigned specific modules it can manage.
- **Team**: Empty role, without permissions by default.
- **Guest**: Empty role, without permissions by default.

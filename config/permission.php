<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Twill permission
    |--------------------------------------------------------------------------
    |
    | This allows you to set up the level of management of the permissions.
    |
    | Three levels exists
    | - role: this manages the access of modules based on the role of a user.
    | - roleGroup: this manages the access of modules for a group of users.
    | - roleGroupModule: this manages the permission of items within a module.
    |
     */

    'level' => 'role',
    'modules' => [], // List of the modules the application have to manage access
];

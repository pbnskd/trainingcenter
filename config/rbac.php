<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Super Admin
    |--------------------------------------------------------------------------
    | This role bypasses all checks (handled in AuthServiceProvider or Gate).
    | We define the name here to avoid hardcoding strings across the app.
    */
    'super_admin' => 'Super Admin',

    /*
    |--------------------------------------------------------------------------
    | Permission List
    |--------------------------------------------------------------------------
    | All possible permissions in the application.
    | Organized by module for clarity.
    */
    'permissions' => [
        // User Module
        'user-list',
        'user-create',
        'user-edit',
        'user-delete',

        // Role Module
        'role-list',
        'role-create',
        'role-edit',
        'role-delete',

        // Permission Module
        'permission-list',
        'permission-create',
        'permission-edit',
        'permission-delete',
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Assignments
    |--------------------------------------------------------------------------
    | Define roles and the specific permissions they should have by default.
    | The 'Super Admin' is usually handled separately, but included for completeness.
    */
    'roles' => [
        'Admin' => [
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'role-list', 'role-create', 'role-edit', 'role-delete',
            'permission-list', 'permission-create', 'permission-edit', 'permission-delete',
        ],
        'Staff' => [
            'user-list',
            'role-list',
        ],
        'User' => [
            // Basic users might not need specific permission nodes if they just use the app
        ],
    ],
];
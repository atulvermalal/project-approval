<?php

namespace App\Support;

class AccessControl
{
    public const DEFAULT_PERMISSIONS = [
        'dashboard.view' => 'View dashboard',
        'projects.create' => 'Create projects',
        'projects.view_all' => 'View all projects',
        'projects.approve' => 'Approve and reject projects',
        'users.manage' => 'Create and manage users',
        'roles.manage' => 'Create and manage roles',
    ];

    public const LEGACY_ROLE_PERMISSIONS = [
        'admin' => [
            'dashboard.view',
            'projects.create',
            'projects.view_all',
            'projects.approve',
            'users.manage',
            'roles.manage',
        ],
        'user' => [
            'dashboard.view',
            'projects.create',
        ],
    ];

    public static function permissionsForRole(?string $role): array
    {
        return self::LEGACY_ROLE_PERMISSIONS[$role ?? 'user'] ?? [];
    }
}

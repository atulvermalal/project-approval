<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_role_and_assign_permissions(): void
    {
        $admin = User::factory()->admin()->create();
        $permissions = Permission::query()
            ->whereIn('name', ['dashboard.view', 'projects.view_all'])
            ->pluck('id')
            ->all();

        $response = $this->actingAs($admin)->post(route('admin.roles.store'), [
            'label' => 'Reviewer',
            'permissions' => $permissions,
        ]);

        $response->assertRedirect(route('admin.roles.index'));

        $role = Role::query()->where('label', 'Reviewer')->first();

        $this->assertNotNull($role);
        $actualPermissions = $role->permissions()->pluck('permissions.id')->sort()->values()->all();
        sort($permissions);

        $this->assertSame($permissions, $actualPermissions);
    }

    public function test_non_admin_cannot_open_role_management_route(): void
    {
        $user = User::factory()->create();
        $permissionId = Permission::query()->where('name', 'dashboard.view')->value('id');

        $this->actingAs($user)->post(route('admin.roles.store'), [
            'label' => 'Blocked Role',
            'permissions' => [$permissionId],
        ])->assertForbidden();
    }
}

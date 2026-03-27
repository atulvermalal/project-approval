<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        $now = now();

        DB::table('permissions')->insert([
            ['name' => 'dashboard.view', 'label' => 'View dashboard', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'projects.create', 'label' => 'Create projects', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'projects.view_all', 'label' => 'View all projects', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'projects.approve', 'label' => 'Approve and reject projects', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'users.manage', 'label' => 'Create and manage users', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'roles.manage', 'label' => 'Create and manage roles', 'created_at' => $now, 'updated_at' => $now],
        ]);

        DB::table('roles')->insert([
            ['name' => 'admin', 'label' => 'Administrator', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'user', 'label' => 'User', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $permissions = DB::table('permissions')->pluck('id', 'name');
        $roles = DB::table('roles')->pluck('id', 'name');

        $adminPermissions = $permissions->values()->map(fn (int $permissionId) => [
            'role_id' => $roles['admin'],
            'permission_id' => $permissionId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $userPermissions = collect([
            'dashboard.view',
            'projects.create',
        ])->map(fn (string $permission) => [
            'role_id' => $roles['user'],
            'permission_id' => $permissions[$permission],
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('permission_role')->insert([
            ...$adminPermissions->all(),
            ...$userPermissions->all(),
        ]);

        DB::table('users')
            ->where('role', 'admin')
            ->update(['role_id' => $roles['admin']]);

        DB::table('users')
            ->where(function ($query) {
                $query->whereNull('role')->orWhere('role', 'user');
            })
            ->update(['role_id' => $roles['user'], 'role' => 'user']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};

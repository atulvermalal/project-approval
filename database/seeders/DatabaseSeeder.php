<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::query()->where('name', 'admin')->first();
        $userRole = Role::query()->where('name', 'user')->first();

        User::query()->updateOrCreate([
            'email' => 'atulvermalal@gmail.com',
        ], [
            'name' => 'Admin User',
            'role' => 'admin',
            'role_id' => $adminRole?->id,
            'password' => bcrypt('password'),
        ]);

        User::query()->updateOrCreate([
            'email' => 'vatul7700@gmail.com',
        ], [
            'name' => 'Project User',
            'role' => 'user',
            'role_id' => $userRole?->id,
            'password' => bcrypt('password'),
        ]);
    }
}

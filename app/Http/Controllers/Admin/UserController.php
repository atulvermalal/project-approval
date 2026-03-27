<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManagedUserRequest;
use App\Http\Requests\Admin\UpdateManagedUserRequest;
use App\Http\Requests\Admin\UpdateManagedUserRoleRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()
                ->with('roleRecord')
                ->withCount('projects')
                ->where('role', '!=', 'admin')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::query()->orderBy('label')->get(),
        ]);
    }

    public function store(StoreManagedUserRequest $request): RedirectResponse
    {
        $role = Role::query()->findOrFail($request->integer('role_id'));

        User::query()->create([
            'name' => $request->string('name')->value(),
            'email' => $request->string('email')->lower()->value(),
            'password' => $request->string('password')->value(),
            'role' => $role->name,
            'role_id' => $role->id,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User created and role assigned successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user->load('roleRecord'),
            'roles' => Role::query()->orderBy('label')->get(),
        ]);
    }

    public function update(UpdateManagedUserRequest $request, User $user): RedirectResponse
    {
        $role = Role::query()->findOrFail($request->integer('role_id'));

        $payload = [
            'name' => $request->string('name')->value(),
            'email' => $request->string('email')->lower()->value(),
            'role' => $role->name,
            'role_id' => $role->id,
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->string('password')->value();
        }

        $user->update($payload);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "User \"{$user->name}\" updated successfully.");
    }

    public function updateRole(UpdateManagedUserRoleRequest $request, User $user): RedirectResponse
    {
        $role = Role::query()->findOrFail($request->integer('role_id'));

        $user->update([
            'role' => $role->name,
            'role_id' => $role->id,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Role updated for {$user->name}.");
    }
}

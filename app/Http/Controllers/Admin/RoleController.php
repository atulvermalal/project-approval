<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::query()->with('permissions')->withCount('users')->orderBy('label')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', [
            'permissions' => Permission::query()->orderBy('label')->get(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $label = $request->string('label')->trim()->value();
        $baseName = Str::of($label)->lower()->slug('_')->value();
        $name = $baseName;
        $suffix = 1;

        while (Role::query()->where('name', $name)->exists()) {
            $suffix++;
            $name = "{$baseName}_{$suffix}";
        }

        $role = Role::query()->create([
            'name' => $name,
            'label' => $label,
        ]);

        $role->permissions()->sync($request->validated('permissions'));

        return redirect()
            ->route('admin.roles.index')
            ->with('status', "Role \"{$role->label}\" created successfully.");
    }

    public function edit(Role $role): View
    {
        $role->load('permissions');

        return view('admin.roles.edit', [
            'role' => $role,
            'permissions' => Permission::query()->orderBy('label')->get(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $role->update([
            'label' => $request->string('label')->trim()->value(),
        ]);

        $role->permissions()->sync($request->validated('permissions'));

        return redirect()
            ->route('admin.roles.index')
            ->with('status', "Role \"{$role->label}\" updated successfully.");
    }
}

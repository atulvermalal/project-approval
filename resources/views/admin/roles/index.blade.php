@php($title = 'Roles')
@extends('layouts.app')

@section('content')
    @include('admin.partials.navigation')

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Roles</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.roles.create') }}">Create Role</a>
    </div>

    <div class="content-card">
        <div class="p-3 border-bottom">
            <div class="fw-semibold">Role Index</div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Role</th>
                        <th>Users</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td class="fw-semibold">{{ $role->label }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($role->permissions as $permission)
                                        <span class="badge-soft">{{ $permission->label }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.roles.edit', $role) }}">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No roles available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $roles->links() }}
        </div>
    </div>
@endsection

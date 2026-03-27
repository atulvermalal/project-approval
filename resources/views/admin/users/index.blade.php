@php($title = 'Users')
@extends('layouts.app')

@section('content')
    @include('admin.partials.navigation')

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Users</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('admin.users.create') }}">Create User</a>
    </div>

    <div class="content-card">
        <div class="p-3 border-bottom">
            <div class="fw-semibold">User Index</div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Projects</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge text-bg-{{ $user->role_name === 'admin' ? 'primary' : 'secondary' }}">
                                    {{ $user->roleRecord?->label ?? ucfirst($user->role_name) }}
                                </span>
                            </td>
                            <td>{{ $user->projects_count }}</td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit', $user) }}">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No users available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $users->links() }}
        </div>
    </div>
@endsection

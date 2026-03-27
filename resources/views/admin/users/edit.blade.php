@php($title = 'Edit User')
@extends('layouts.app')

@section('content')
    @include('admin.partials.navigation')

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Edit User</h1>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.users.index') }}">Back to Users</a>
    </div>

    <div class="content-card">
        <div class="p-4">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-6">
                    <label class="form-label" for="name">Name</label>
                    <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name', $user->name) }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="password">New Password</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="Leave blank to keep current password">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="role_id">Role</label>
                    <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('role_id', $user->role_id) === (string) $role->id)>{{ $role->label }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Update User</button>
                </div>
            </form>
        </div>
    </div>
@endsection

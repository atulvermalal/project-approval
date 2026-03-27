@php($title = 'Edit Role')
@extends('layouts.app')

@section('content')
    @include('admin.partials.navigation')

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Edit Role</h1>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('admin.roles.index') }}">Back to Roles</a>
    </div>

    <div class="content-card">
        <div class="p-4">
            <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label" for="label">Role name</label>
                    <input class="form-control @error('label') is-invalid @enderror" id="label" name="label" type="text" value="{{ old('label', $role->label) }}">
                    @error('label')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="row g-3">
                    @foreach ($permissions as $permission)
                        <div class="col-md-6">
                            <label class="border rounded p-3 d-block h-100">
                                <input class="form-check-input me-2" type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permissions', $role->permissions->pluck('id')->all())))>
                                <span class="fw-semibold">{{ $permission->label }}</span>
                                <div class="small text-muted">{{ $permission->name }}</div>
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('permissions')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
                <div class="mt-4">
                    <button class="btn btn-primary" type="submit">Update Role</button>
                </div>
            </form>
        </div>
    </div>
@endsection

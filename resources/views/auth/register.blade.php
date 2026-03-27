@php($title = 'Register')
@extends('layouts.app')

@section('content')
    <div class="row align-items-center g-4 g-xl-5 min-vh-75">
        <div class="col-lg-6 order-2 order-lg-1">
            <div class="shell-card p-4 p-lg-5">
                <div class="mb-4">
                    <h1 class="h2 fw-bold mb-2">Create your account</h1>
                </div>

                <form method="POST" action="{{ route('register.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="name">Full name</label>
                        <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Enter your full name" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">Please enter your name.</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">Please enter a valid email.</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required minlength="8" placeholder="Minimum 8 characters">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">Password must be at least 8 characters.</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold" for="password_confirmation">Confirm password</label>
                        <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required minlength="8" placeholder="Retype your password">
                        <div class="invalid-feedback">Please confirm your password.</div>
                    </div>

                    <button class="btn btn-primary w-100 py-3 fw-semibold" type="submit">Create account</button>
                </form>

                <p class="text-muted mt-4 mb-0">Already registered? <a href="{{ route('login') }}" class="fw-semibold">Back to login</a></p>
            </div>
        </div>

        <div class="col-lg-5 ms-lg-auto order-1 order-lg-2">
            <div class="pe-xl-4">
                <h2 class="display-5 fw-bold mb-3">Register</h2>
            </div>
        </div>
    </div>
@endsection

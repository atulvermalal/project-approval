@php($title = 'Login')
@extends('layouts.app')

@section('content')
    <div class="row align-items-center g-4 g-xl-5 min-vh-75">
        <div class="col-lg-6">
            <div class="pe-xl-5">
                <h1 class="display-4 fw-bold mb-3">Login</h1>
            </div>
        </div>

        <div class="col-lg-5 ms-lg-auto">
            <div class="shell-card p-4 p-lg-5">
                <div class="mb-4">
                    <h2 class="h2 fw-bold mb-2">Sign in</h2>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="email">Email</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="name@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">Please enter a valid email address.</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="password">Password</label>
                        <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" placeholder="Enter password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @else
                            <div class="invalid-feedback">Password is required.</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100 py-3 fw-semibold" type="submit">Login to dashboard</button>
                </form>

                <div class="mt-4 pt-4 border-top">
                    <p class="text-muted mb-2">Need an account? <a href="{{ route('register') }}" class="fw-semibold">Register here</a></p>
                  
                </div>
            </div>
        </div>
    </div>
@endsection

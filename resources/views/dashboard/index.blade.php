@php($title = 'Dashboard')
@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-4">
        <div>
            <h1 class="h2 mb-1">Dashboard</h1>
        </div>
        <div class="small text-muted">
            <a href="{{ route('dashboard') }}">Home</a>
            <span class="mx-1">/</span>
            <span>Dashboard</span>
        </div>
    </div>

    <section class="tab-pane-panel {{ $activeTab === 'dashboard' ? 'active' : '' }}">
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="small-box bg-info">
                    <span class="metric-icon">+</span>
                    <h3>{{ $stats['submitted']['count'] }}</h3>
                    <p>Total Projects</p>
                    <a class="small-box-footer" href="{{ route('projects.index') }}">More info</a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="small-box bg-success-soft">
                    <span class="metric-icon">%</span>
                    <h3>{{ $isAdmin ? $stats['approved']['percent'].'%' : $stats['approved']['count'] }}</h3>
                    <p>{{ $isAdmin ? 'Approval Rate' : 'Approved Projects' }}</p>
                    <a class="small-box-footer" href="{{ route('projects.index') }}">More info</a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="small-box bg-warning-soft">
                    <span class="metric-icon">{{ $isAdmin ? 'U' : 'R' }}</span>
                    <h3>{{ $isAdmin ? ($users->count() ?: 1) : $stats['rejected']['count'] }}</h3>
                    <p>{{ $isAdmin ? 'Managed Users' : 'Rejected Projects' }}</p>
                    <a class="small-box-footer" href="{{ $isAdmin && auth()->user()->hasPermission('users.manage') ? route('admin.users.index') : route('projects.index') }}">More info</a>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="small-box bg-danger-soft">
                    <span class="metric-icon">!</span>
                    <h3>{{ $stats['pending']['count'] }}</h3>
                    <p>{{ $isAdmin ? 'Pending Actions' : 'Pending Projects' }}</p>
                    <a class="small-box-footer" href="{{ route('projects.index') }}">More info</a>
                </div>
            </div>
        </div>
    </section>

    <section class="tab-pane-panel {{ $activeTab === 'projects' ? 'active' : '' }}">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="content-card h-100">
                    <div class="p-4">
                        <div class="small text-uppercase text-muted fw-semibold mb-2">Project Index</div>
                        <h2 class="h3 mb-2">Projects now have a separate listing page</h2>
                        <div class="d-flex flex-wrap gap-2">
                            <a class="btn btn-primary" href="{{ route('projects.index') }}">Open Projects</a>
                            @if (auth()->user()->hasPermission('projects.create'))
                                <a class="btn btn-outline-primary" href="{{ route('projects.create') }}">Create Project</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content-card h-100">
                    <div class="p-4">
                        <div class="small text-uppercase text-muted fw-semibold mb-2">Project Summary</div>
                        <h2 class="h3 mb-2">Quick status overview stays on dashboard</h2>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="mini-stat">
                                    <div class="text-muted small">Pending</div>
                                    <div class="h5 mb-0">{{ $stats['pending']['count'] }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mini-stat">
                                    <div class="text-muted small">Approved</div>
                                    <div class="h5 mb-0">{{ $stats['approved']['count'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

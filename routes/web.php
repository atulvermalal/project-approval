<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProjectAttachmentController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('/projects', [ProjectController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('projects.index');

    Route::get('/projects/create', [ProjectController::class, 'create'])
        ->middleware('permission:projects.create')
        ->name('projects.create');

    Route::post('/projects/validate', [ProjectController::class, 'validateStore'])
        ->middleware('permission:projects.create')
        ->name('projects.validate');

    Route::post('/projects', [ProjectController::class, 'store'])
        ->middleware('permission:projects.create')
        ->name('projects.store');

    Route::get('/projects/{project}', [ProjectController::class, 'show'])
        ->middleware('permission:dashboard.view')
        ->name('projects.show');

    Route::get('/projects/{project}/attachments/{attachment}', [ProjectAttachmentController::class, 'show'])
        ->middleware('permission:dashboard.view')
        ->whereNumber('attachment')
        ->name('projects.attachments.show');

    Route::get('/projects/{project}/attachments/{attachment}/download', [ProjectAttachmentController::class, 'download'])
        ->middleware('permission:dashboard.view')
        ->whereNumber('attachment')
        ->name('projects.attachments.download');

    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
        ->middleware('permission:dashboard.view')
        ->name('projects.destroy');

    Route::patch('/projects/{project}/approve', [ProjectController::class, 'approve'])
        ->middleware('permission:projects.approve')
        ->name('projects.approve');

    Route::patch('/projects/{project}/reject', [ProjectController::class, 'reject'])
        ->middleware('permission:projects.approve')
        ->name('projects.reject');

    Route::post('/projects/bulk-action', [ProjectController::class, 'bulk'])
        ->middleware('permission:projects.approve')
        ->name('projects.bulk');

    Route::get('/admin/roles', [RoleController::class, 'index'])
        ->middleware(['role:admin', 'permission:roles.manage'])
        ->name('admin.roles.index');

    Route::get('/admin/roles/create', [RoleController::class, 'create'])
        ->middleware(['role:admin', 'permission:roles.manage'])
        ->name('admin.roles.create');

    Route::get('/admin/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware(['role:admin', 'permission:roles.manage'])
        ->name('admin.roles.edit');

    Route::post('/admin/roles', [RoleController::class, 'store'])
        ->middleware(['role:admin', 'permission:roles.manage'])
        ->name('admin.roles.store');

    Route::put('/admin/roles/{role}', [RoleController::class, 'update'])
        ->middleware(['role:admin', 'permission:roles.manage'])
        ->name('admin.roles.update');

    Route::get('/admin/users', [UserController::class, 'index'])
        ->middleware(['role:admin', 'permission:users.manage'])
        ->name('admin.users.index');

    Route::get('/admin/users/create', [UserController::class, 'create'])
        ->middleware(['role:admin', 'permission:users.manage'])
        ->name('admin.users.create');

    Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware(['role:admin', 'permission:users.manage'])
        ->name('admin.users.edit');

    Route::post('/admin/users', [UserController::class, 'store'])
        ->middleware(['role:admin', 'permission:users.manage'])
        ->name('admin.users.store');

    Route::put('/admin/users/{user}', [UserController::class, 'update'])
        ->middleware(['role:admin', 'permission:users.manage'])
        ->name('admin.users.update');

    Route::patch('/admin/users/{user}/role', [UserController::class, 'updateRole'])
        ->middleware(['role:admin', 'permission:users.manage'])
        ->name('admin.users.role.update');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

<?php

use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/projects', [ProjectController::class, 'store'])->middleware('permission:projects.create');
    Route::patch('/projects/{project}/approve', [ProjectController::class, 'approve'])->middleware('permission:projects.approve');
});

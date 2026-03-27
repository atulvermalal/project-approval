<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $activeTab = $request->string('tab')->value() ?: 'dashboard';
        $filters = $request->only(['status', 'submitter', 'date_from', 'date_to', 'sort', 'tab']);

        $baseQuery = Project::query()
            ->with(['user', 'approvals.admin'])
            ->when(! $user->hasPermission('projects.view_all'), fn (Builder $query) => $query->where('user_id', $user->id));

        $statsBaseQuery = clone $baseQuery;
        $statsCollection = $statsBaseQuery->get();
        $totalProjects = max($statsCollection->count(), 1);

        $projects = $baseQuery
            ->when($request->filled('status'), fn (Builder $query) => $query->where('status', $request->string('status')))
            ->when($request->filled('submitter') && $user->hasPermission('projects.view_all'), function (Builder $query) use ($request) {
                $term = $request->string('submitter');
                $query->whereHas('user', function (Builder $userQuery) use ($term) {
                    $userQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('date_from'), fn (Builder $query) => $query->whereDate('submitted_at', '>=', $request->string('date_from')))
            ->when($request->filled('date_to'), fn (Builder $query) => $query->whereDate('submitted_at', '<=', $request->string('date_to')))
            ->when(
                $request->string('sort')->value(),
                function (Builder $query, string $sort) {
                    return match ($sort) {
                        'oldest' => $query->orderBy('submitted_at'),
                        'status' => $query->orderBy('status')->orderByDesc('submitted_at'),
                        'updated' => $query->orderByDesc('updated_at'),
                        default => $query->orderByDesc('submitted_at'),
                    };
                },
                fn (Builder $query) => $query->orderByDesc('submitted_at')
            )
            ->paginate(10)
            ->withQueryString();

        $stats = collect(['submitted', 'pending', 'rejected', 'approved'])->mapWithKeys(
            fn (string $status) => [
                $status => [
                    'count' => $status === 'submitted'
                        ? $statsCollection->count()
                        : $statsCollection->where('status', $status)->count(),
                    'percent' => $status === 'submitted'
                        ? 100
                        : round(($statsCollection->where('status', $status)->count() / $totalProjects) * 100),
                ],
            ]
        );

        $users = $user->hasPermission('users.manage')
            ? User::query()
                ->with('roleRecord')
                ->withCount('projects')
                ->latest()
                ->take(10)
                ->get()
            : collect();

        $roles = $user->hasPermission('roles.manage') || $user->hasPermission('users.manage')
            ? Role::query()
                ->with('permissions')
                ->withCount('users')
                ->orderBy('label')
                ->get()
            : collect();

        $permissions = $user->hasPermission('roles.manage')
            ? Permission::query()->orderBy('label')->get()
            : collect();

        return view('dashboard.index', [
            'projects' => $projects,
            'stats' => $stats,
            'filters' => $filters,
            'activeTab' => $activeTab,
            'isAdmin' => $user->hasRole('admin'),
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }
}

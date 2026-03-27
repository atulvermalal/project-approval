<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkProjectActionRequest;
use App\Http\Requests\ProjectApprovalRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Models\Project;
use App\Services\ProjectApprovalService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function __construct(protected ProjectApprovalService $approvalService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $filters = $request->only(['status', 'submitter', 'date_from', 'date_to', 'sort']);

        $projects = Project::query()
            ->with(['user', 'approvals.admin'])
            ->when(! $user->hasPermission('projects.view_all'), fn (Builder $query) => $query->where('user_id', $user->id))
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

        return view('projects.index', [
            'projects' => $projects,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function validateStore(Request $request): JsonResponse
    {
        return $this->validatePayload($request, ProjectStoreRequest::baseRules());
    }

    public function store(ProjectStoreRequest $request): RedirectResponse
    {
        $attachments = collect($request->file('files', []))
            ->map(fn ($file) => [
                'name' => $file->getClientOriginalName(),
                'path' => $file->store('projects', 'public'),
            ])
            ->values()
            ->all();

        $project = $request->user()->projects()->create([
            ...$request->safe()->only(['title', 'description']),
            'attachments' => $attachments,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->approvalService->submit($project->fresh('user'));

        return redirect()
            ->route('projects.index')
            ->with('status', 'Project submitted and queued notification prepared.');
    }

    public function show(Project $project): View
    {
        $this->authorize('view', $project);

        return view('projects.show', [
            'project' => $project->load(['user', 'approvals.admin', 'auditLogs.user']),
        ]);
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $title = $project->title;
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('status', "Project \"{$title}\" deleted successfully.");
    }

    public function approve(ProjectApprovalRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('approve', $project);

        $this->approvalService->decide(
            $project,
            $request->user(),
            'approved',
            $request->string('reason')->value(),
        );

        return redirect()
            ->route('projects.index')
            ->with('status', "Project \"{$project->title}\" approved.");
    }

    public function reject(ProjectApprovalRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('approve', $project);

        $this->approvalService->decide(
            $project,
            $request->user(),
            'rejected',
            $request->string('reason')->value(),
        );

        return redirect()
            ->route('projects.index')
            ->with('status', "Project \"{$project->title}\" rejected.");
    }

    public function bulk(BulkProjectActionRequest $request): RedirectResponse
    {
        $admin = $request->user();
        $status = $request->string('status')->value();
        $reason = $request->string('reason')->value();

        $projects = Project::query()
            ->whereIn('id', $request->validated('project_ids'))
            ->where('status', 'pending')
            ->get();

        foreach ($projects as $project) {
            $this->authorize('approve', $project);
            $this->approvalService->decide($project, $admin, $status, $reason);
        }

        return redirect()
            ->route('projects.index')
            ->with('status', ucfirst($status).' action applied to '.$projects->count().' project(s).');
    }

    private function validatePayload(Request $request, array $rules): JsonResponse
    {
        $validator = Validator::make($request->all(), $rules);

        return response()->json([
            'valid' => ! $validator->fails(),
            'errors' => $validator->errors()->toArray(),
        ], $validator->fails() ? 422 : 200);
    }
}

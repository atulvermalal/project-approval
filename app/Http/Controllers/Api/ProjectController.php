<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectApprovalRequest;
use App\Http\Requests\ProjectStoreRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectApprovalService;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function __construct(protected ProjectApprovalService $approvalService)
    {
    }

    public function store(ProjectStoreRequest $request): ProjectResource
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

        return new ProjectResource($this->approvalService->submit($project->fresh('user')));
    }

    public function approve(ProjectApprovalRequest $request, Project $project): JsonResponse
    {
        $this->authorize('approve', $project);

        $approved = $this->approvalService->decide(
            $project,
            $request->user(),
            'approved',
            $request->string('reason')->value(),
        );

        return (new ProjectResource($approved))
            ->response()
            ->setStatusCode(200);
    }
}

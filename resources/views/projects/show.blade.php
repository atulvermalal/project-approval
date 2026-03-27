@php($title = 'Project Details')
@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">{{ $project->title }}</h1>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('projects.index') }}">Back to Projects</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="content-card">
                <div class="p-4">
                    <div class="small text-uppercase text-muted fw-semibold mb-2">Description</div>
                    <p class="mb-4">{{ $project->description }}</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mini-stat">
                                <div class="text-muted small">Submitter</div>
                                <div class="fw-semibold">{{ $project->user->name }}</div>
                                <div class="small text-muted">{{ $project->user->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mini-stat">
                                <div class="text-muted small">Status</div>
                                <div class="fw-semibold">{{ ucfirst($project->status) }}</div>
                                <div class="small text-muted">Updated {{ $project->updated_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>

                    @if (!empty($project->attachments))
                        <div class="mt-4">
                            <div class="small text-uppercase text-muted fw-semibold mb-2">Attachments</div>
                            <div class="d-grid gap-2">
                                @foreach ($project->attachments as $index => $attachment)
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 border rounded px-3 py-2">
                                        <span class="fw-semibold">{{ $attachment['name'] }}</span>
                                        <div class="d-flex gap-2">
                                            <a class="btn btn-sm btn-outline-primary" href="{{ route('projects.attachments.show', [$project, $index]) }}" target="_blank">View</a>
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('projects.attachments.download', [$project, $index]) }}">Download</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="content-card">
                <div class="p-3 border-bottom">
                    <div class="fw-semibold">Approval History</div>
                </div>
                <div class="p-3 d-grid gap-2">
                    @forelse ($project->approvals as $approval)
                        <div class="border rounded p-3">
                            <div class="fw-semibold text-capitalize">{{ $approval->status }}</div>
                            <div class="small text-muted">By {{ $approval->admin->name }} on {{ optional($approval->acted_at)->format('d M Y h:i A') }}</div>
                            <div class="small mt-1">{{ $approval->reason ?: 'No reason provided.' }}</div>
                        </div>
                    @empty
                        <div class="text-muted small">No approval history yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@php($title = 'Projects')
@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Projects</h1>
        </div>
        @if (auth()->user()->hasPermission('projects.create'))
            <a class="btn btn-primary" href="{{ route('projects.create') }}">Create Project</a>
        @endif
    </div>

    <div class="content-card">
        <div class="p-3 border-bottom">
            <div class="fw-semibold">Project List</div>
        </div>
        <div class="p-3">
            <form method="GET" action="{{ route('projects.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All statuses</option>
                        @foreach (['pending', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                @if (auth()->user()->hasPermission('projects.view_all'))
                    <div class="col-md-3">
                        <label class="form-label" for="submitter">Submitter</label>
                        <input class="form-control" id="submitter" name="submitter" type="text" value="{{ $filters['submitter'] ?? '' }}" placeholder="Name or email">
                    </div>
                @endif

                <div class="col-md-2">
                    <label class="form-label" for="date_from">From</label>
                    <input class="form-control" id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="date_to">To</label>
                    <input class="form-control" id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="sort">Sort</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="latest" @selected(($filters['sort'] ?? 'latest') === 'latest')>Latest</option>
                        <option value="oldest" @selected(($filters['sort'] ?? '') === 'oldest')>Oldest</option>
                        <option value="status" @selected(($filters['sort'] ?? '') === 'status')>Status</option>
                        <option value="updated" @selected(($filters['sort'] ?? '') === 'updated')>Updated</option>
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-dark" type="submit">Apply filters</button>
                    <a class="btn btn-outline-secondary" href="{{ route('projects.index') }}">Reset</a>
                </div>
            </form>

            @if (auth()->user()->hasPermission('projects.approve'))
                <form method="POST" action="{{ route('projects.bulk') }}" class="border rounded p-3 mb-4 bulk-action-form">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-4">
                            <label class="form-label">Bulk action</label>
                            <select class="form-select" name="status" required data-bulk-status>
                                <option value="approved">Approve selected</option>
                                <option value="rejected">Reject selected</option>
                            </select>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label">Reason</label>
                            <input class="form-control" type="text" name="reason" maxlength="1000" placeholder="Required only for rejection" data-bulk-reason>
                        </div>
                        <div class="col-lg-2 d-grid">
                            <button class="btn btn-primary" type="submit">Run</button>
                        </div>
                    </div>
                </form>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            @if (auth()->user()->hasPermission('projects.approve'))
                                <th class="text-center"><input class="form-check-input" type="checkbox" data-select-all></th>
                            @endif
                            <th>Project</th>
                            <th>Submitter</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Updated</th>
                            <th>History</th>
                            <th>View</th>
                            <th>Delete</th>
                            @if (auth()->user()->hasPermission('projects.approve'))
                                <th>Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                @if (auth()->user()->hasPermission('projects.approve'))
                                    <td class="text-center">
                                        <input class="form-check-input bulk-project-checkbox" type="checkbox" value="{{ $project->id }}" @disabled($project->status !== 'pending')>
                                    </td>
                                @endif
                                <td>
                                    <div class="fw-semibold">{{ $project->title }}</div>
                                    <div class="small text-muted">{{ \Illuminate\Support\Str::limit($project->description, 90) }}</div>
                                    @if (!empty($project->attachments))
                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                            @foreach ($project->attachments as $index => $attachment)
                                                <a class="badge-soft" href="{{ route('projects.attachments.show', [$project, $index]) }}" target="_blank">{{ $attachment['name'] }}</a>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $project->user->name }}</div>
                                    <div class="small text-muted">{{ $project->user->email }}</div>
                                </td>
                                <td>{{ optional($project->submitted_at)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge text-bg-{{ match($project->status) { 'pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger', default => 'secondary' } }}">
                                        {{ ucfirst($project->status) }}
                                    </span>
                                </td>
                                <td>{{ $project->updated_at->diffForHumans() }}</td>
                                <td>
                                    @if ($project->approvals->isEmpty())
                                        <span class="small text-muted">Awaiting first decision</span>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#history-{{ $project->id }}">View history</button>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-outline-dark" href="{{ route('projects.show', $project) }}">Show</a>
                                </td>
                                <td>
                                    @can('delete', $project)
                                        <form method="POST" action="{{ route('projects.destroy', $project) }}" data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                        </form>
                                    @else
                                        <span class="small text-muted">Locked</span>
                                    @endcan
                                </td>
                                @if (auth()->user()->hasPermission('projects.approve'))
                                    <td>
                                        @if ($project->status === 'pending')
                                            <div class="d-grid gap-2">
                                                <form method="POST" action="{{ route('projects.approve', $project) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button class="btn btn-sm btn-success w-100" type="submit">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('projects.reject', $project) }}" class="decision-form">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input class="form-control form-control-sm mb-2" type="text" name="reason" placeholder="Reason required" required>
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button class="btn btn-sm btn-outline-danger w-100" type="submit">Reject</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="small text-muted">Decision locked</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>

                            @if ($project->approvals->isNotEmpty())
                                <tr class="collapse" id="history-{{ $project->id }}">
                                    <td colspan="{{ auth()->user()->hasPermission('projects.approve') ? 10 : 8 }}" class="bg-light">
                                        <div class="py-2 d-grid gap-2">
                                            @foreach ($project->approvals as $approval)
                                                <div class="border rounded p-2 bg-white">
                                                    <div class="fw-semibold text-capitalize">{{ $approval->status }}</div>
                                                    <div class="small text-muted">By {{ $approval->admin->name }} on {{ optional($approval->acted_at)->format('d M Y h:i A') }}</div>
                                                    <div class="small">{{ $approval->reason ?: 'No reason provided.' }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="{{ auth()->user()->hasPermission('projects.approve') ? 10 : 8 }}" class="text-center py-5 text-muted">No projects match the current filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $projects->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.decision-form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                const reason = form.querySelector('input[name="reason"]');

                if (!reason.value.trim()) {
                    event.preventDefault();
                    reason.classList.add('is-invalid');
                    reason.focus();
                }
            });
        });

        const selectAll = document.querySelector('[data-select-all]');
        const bulkForm = document.querySelector('.bulk-action-form');

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                document.querySelectorAll('.bulk-project-checkbox:not(:disabled)').forEach((checkbox) => {
                    checkbox.checked = selectAll.checked;
                });
            });
        }

        if (bulkForm) {
            const bulkStatus = bulkForm.querySelector('[data-bulk-status]');
            const bulkReason = bulkForm.querySelector('[data-bulk-reason]');

            const syncBulkReason = () => {
                bulkReason.required = bulkStatus.value === 'rejected';
            };

            bulkStatus.addEventListener('change', syncBulkReason);
            syncBulkReason();

            bulkForm.addEventListener('submit', (event) => {
                const selected = document.querySelectorAll('.bulk-project-checkbox:checked');
                bulkForm.querySelectorAll('input[name="project_ids[]"][data-generated="true"]').forEach((input) => input.remove());

                if (!selected.length) {
                    event.preventDefault();
                    window.alert('Select at least one pending project first.');
                    return;
                }

                if (bulkStatus.value === 'rejected' && !bulkReason.value.trim()) {
                    event.preventDefault();
                    bulkReason.focus();
                    return;
                }

                selected.forEach((checkbox) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'project_ids[]';
                    input.value = checkbox.value;
                    input.dataset.generated = 'true';
                    bulkForm.appendChild(input);
                });
            });
        }

        document.querySelectorAll('[data-confirm-delete]').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!window.confirm('Are you sure you want to delete this project?')) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endpush

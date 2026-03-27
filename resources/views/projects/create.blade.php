@php($title = 'Create Project')
@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Create Project</h1>
        </div>
        <a class="btn btn-outline-secondary" href="{{ route('projects.index') }}">Back to Projects</a>
    </div>

    <div class="content-card">
        <div class="p-4">
            <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="title">Project title</label>
                    <input class="form-control @error('title') is-invalid @enderror" id="title" name="title" type="text" value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Project title is required.</div>@enderror
                    <div class="small text-danger mt-1 d-none" data-live-error="title"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" minlength="20" maxlength="5000" required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@else<div class="invalid-feedback">Minimum 20 characters required.</div>@enderror
                    <div class="small text-danger mt-1 d-none" data-live-error="description"></div>
                    <div class="form-text"><span data-description-count>0</span>/5000 characters</div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="files">Attachments</label>
                    <input class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror" id="files" name="files[]" type="file" multiple>
                    @error('files')<div class="invalid-feedback">{{ $message }}</div>@elseif ($errors->has('files.*'))<div class="invalid-feedback">{{ $errors->first('files.*') }}</div>@endif
                    <div class="small text-danger mt-1 d-none" data-live-error="files"></div>
                    <div class="form-text" data-file-preview>No files selected.</div>
                </div>
                <button class="btn btn-primary" type="submit">Submit Project</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.needs-validation').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            });
        });

        const description = document.querySelector('#description');
        const descriptionCount = document.querySelector('[data-description-count]');
        const filesInput = document.querySelector('#files');
        const filePreview = document.querySelector('[data-file-preview]');

        if (description && descriptionCount) {
            const syncDescriptionCount = () => descriptionCount.textContent = description.value.length;
            description.addEventListener('input', syncDescriptionCount);
            syncDescriptionCount();
        }

        if (filesInput && filePreview) {
            filesInput.addEventListener('change', () => {
                const names = Array.from(filesInput.files).map((file) => file.name);
                filePreview.textContent = names.length ? names.join(', ') : 'No files selected.';
            });
        }

        const projectForm = document.querySelector('form[action="{{ route('projects.store') }}"]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const syncLiveError = (field, message) => {
            const input = projectForm?.querySelector(`[name="${field}"]`);
            const feedback = document.querySelector(`[data-live-error="${field}"]`);

            if (!input || !feedback) {
                return;
            }

            input.classList.toggle('is-invalid', Boolean(message));
            feedback.textContent = message ?? '';
            feedback.classList.toggle('d-none', !message);
        };

        const runRemoteValidation = async (field) => {
            if (!projectForm || !csrfToken) {
                return;
            }

            const formData = new FormData(projectForm);

            try {
                const response = await fetch('{{ route('projects.validate') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const payload = await response.json();
                const message = payload.errors?.[field]?.[0] ?? null;
                syncLiveError(field, message);
            } catch (_) {
            }
        };

        ['title', 'description', 'files'].forEach((field) => {
            const input = document.querySelector(`[name="${field === 'files' ? 'files[]' : field}"]`);

            if (input) {
                input.addEventListener(field === 'files' ? 'change' : 'input', () => runRemoteValidation(field));
                input.addEventListener('blur', () => runRemoteValidation(field));
            }
        });
    </script>
@endpush

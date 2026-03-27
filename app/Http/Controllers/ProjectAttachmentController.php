<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectAttachmentController extends Controller
{
    public function show(Project $project, int $attachment): StreamedResponse
    {
        $this->authorize('view', $project);

        $file = $this->resolveAttachment($project, $attachment);

        return Storage::disk('public')->response(
            $file['path'],
            $file['name'] ?? basename($file['path'])
        );
    }

    public function download(Project $project, int $attachment): StreamedResponse
    {
        $this->authorize('view', $project);

        $file = $this->resolveAttachment($project, $attachment);

        return Storage::disk('public')->download(
            $file['path'],
            $file['name'] ?? basename($file['path'])
        );
    }

    private function resolveAttachment(Project $project, int $attachment): array
    {
        $attachments = collect($project->attachments ?? [])->values();
        $file = $attachments->get($attachment);

        abort_if(! is_array($file) || empty($file['path']) || ! Storage::disk('public')->exists($file['path']), 404);

        return $file;
    }
}

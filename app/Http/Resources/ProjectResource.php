<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'submitted_at' => optional($this->submitted_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
            'submitter' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
                'email' => $this->user?->email,
            ],
            'attachments' => collect($this->attachments ?? [])->map(fn (array $file) => [
                'name' => $file['name'] ?? null,
                'path' => $file['path'] ?? null,
                'url' => isset($file['path']) ? asset('storage/'.$file['path']) : null,
            ])->values(),
            'history' => $this->whenLoaded('approvals', fn () => $this->approvals->map(fn ($approval) => [
                'status' => $approval->status,
                'reason' => $approval->reason,
                'acted_at' => optional($approval->acted_at)->toIso8601String(),
                'admin' => $approval->admin?->only(['id', 'name', 'email']),
            ])->values()),
        ];
    }
}

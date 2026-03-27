<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectWorkflowUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public string $projectTitle,
        public string $status,
        public string $message,
        public string $timestamp,
        public string $url,
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('users.'.$this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'project.workflow.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'projectTitle' => $this->projectTitle,
            'status' => $this->status,
            'message' => $this->message,
            'timestamp' => $this->timestamp,
            'url' => $this->url,
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectWorkflowNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Project $project,
        protected string $event,
        protected ?string $reason = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabel = ucfirst($this->project->status);
        $timestamp = now()->format('d M Y h:i A');
        $bodyText = $this->event === 'submitted'
            ? 'Your project has been submitted successfully and is now waiting for review.'
            : "Your project was {$this->project->status} by the approvals team.";

        return (new MailMessage)
            ->subject("Project {$this->event}: {$this->project->title}")
            ->markdown('emails.project-workflow', [
                'recipientName' => $notifiable->name ?? 'Project User',
                'projectTitle' => $this->project->title,
                'statusLabel' => $statusLabel,
                'timestamp' => $timestamp,
                'bodyText' => $bodyText,
                'reason' => $this->reason,
                'dashboardUrl' => url('/dashboard'),
            ]);
    }
}

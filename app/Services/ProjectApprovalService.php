<?php

namespace App\Services;

use App\Events\ProjectWorkflowUpdated;
use App\Models\Approval;
use App\Models\AuditLog;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectWorkflowNotification;
use Illuminate\Support\Facades\DB;

class ProjectApprovalService
{
    public function submit(Project $project): Project
    {
        AuditLog::create([
            'project_id' => $project->id,
            'user_id' => $project->user_id,
            'action' => 'submitted',
            'details' => ['status' => $project->status],
            'created_at' => now(),
        ]);

        $project->user->notify(new ProjectWorkflowNotification($project, 'submitted'));
        ProjectWorkflowUpdated::dispatch(
            $project->user_id,
            $project->title,
            $project->status,
            'Project submitted successfully.',
            now()->format('d M Y h:i A'),
            url('/projects/'.$project->id)
        );

        return $project->fresh(['user', 'approvals.admin', 'auditLogs.user']);
    }

    public function decide(Project $project, User $admin, string $status, ?string $reason = null): Project
    {
        return DB::transaction(function () use ($project, $admin, $status, $reason) {
            if ($status === 'approved') {
                if (DB::connection()->getDriverName() === 'mysql') {
                    DB::statement('SET @approval_user_id = ?', [$admin->id]);
                    DB::statement('SET @approval_action_details = ?', [json_encode(['status' => 'approved'])]);
                    DB::select('CALL sp_approve_project(?)', [$project->id]);
                } else {
                    $project->update([
                        'status' => 'approved',
                        'rejection_reason' => null,
                    ]);

                    AuditLog::create([
                        'project_id' => $project->id,
                        'user_id' => $admin->id,
                        'action' => 'approved',
                        'details' => ['status' => 'approved'],
                        'created_at' => now(),
                    ]);
                }
            } else {
                $project->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                ]);

                AuditLog::create([
                    'project_id' => $project->id,
                    'user_id' => $admin->id,
                    'action' => 'rejected',
                    'details' => ['status' => 'rejected', 'reason' => $reason],
                    'created_at' => now(),
                ]);
            }

            Approval::create([
                'project_id' => $project->id,
                'admin_id' => $admin->id,
                'status' => $status,
                'reason' => $reason,
                'acted_at' => now(),
            ]);

            $project = $project->fresh(['user', 'approvals.admin', 'auditLogs.user']);

            $project->user->notify(new ProjectWorkflowNotification($project, $status, $reason));
            ProjectWorkflowUpdated::dispatch(
                $project->user_id,
                $project->title,
                $project->status,
                $status === 'approved'
                    ? 'Your project was approved.'
                    : 'Your project was rejected.',
                now()->format('d M Y h:i A'),
                url('/projects/'.$project->id)
            );

            return $project;
        });
    }
}

<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.view_all') || $project->user_id === $user->id;
    }

    public function approve(User $user, Project $project): bool
    {
        return $user->hasPermission('projects.approve') && $project->status === 'pending';
    }

    public function delete(User $user, Project $project): bool
    {
        return ($user->hasPermission('projects.view_all') || $project->user_id === $user->id)
            && $project->status === 'pending';
    }
}

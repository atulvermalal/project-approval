<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectWorkflowNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_project_and_notification_is_dispatched(): void
    {
        Notification::fake();
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.store'), [
            'title' => 'City Revamp',
            'description' => 'A complete redesign plan for the city services platform.',
            'files' => [UploadedFile::fake()->create('brief.pdf', 120)],
        ]);

        $response->assertRedirect(route('projects.index'));

        $project = Project::first();

        $this->assertNotNull($project);
        $this->assertSame('pending', $project->status);
        $this->assertDatabaseHas('audit_logs', [
            'project_id' => $project->id,
            'action' => 'submitted',
        ]);

        Notification::assertSentTo($user, ProjectWorkflowNotification::class);
    }

    public function test_admin_can_approve_project_from_api(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'title' => 'Campus Portal',
            'description' => 'Detailed modernization plan for the student campus portal.',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($admin)->patchJson("/api/projects/{$project->id}/approve", [
            'status' => 'approved',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.submitter.email', $user->email);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('approvals', [
            'project_id' => $project->id,
            'admin_id' => $admin->id,
            'status' => 'approved',
        ]);

        Notification::assertSentTo($user, ProjectWorkflowNotification::class);
    }
}

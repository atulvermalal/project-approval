<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_open_project_pages(): void
    {
        $user = User::factory()->create();
        $project = Project::query()->create([
            'user_id' => $user->id,
            'title' => 'My Project',
            'description' => 'This project has enough description text for testing purposes.',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)->get(route('projects.index'))
            ->assertOk()
            ->assertSee('Project List');

        $this->actingAs($user)->get(route('projects.create'))
            ->assertOk()
            ->assertSee('Create Project');

        $this->actingAs($user)->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Approval History');
    }

    public function test_user_can_delete_pending_project(): void
    {
        $user = User::factory()->create();
        $project = Project::query()->create([
            'user_id' => $user->id,
            'title' => 'Delete Me',
            'description' => 'This project will be deleted during the feature test run.',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->delete(route('projects.destroy', $project))
            ->assertRedirect(route('projects.index'));

        $this->assertDatabaseMissing('projects', [
            'id' => $project->id,
        ]);
    }
}

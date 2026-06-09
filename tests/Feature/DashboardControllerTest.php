<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // basic access
    // -------------------------------------------------------------------------

    public function test_dashboard_returns_200(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
    }

    // -------------------------------------------------------------------------
    // KPI counts
    // -------------------------------------------------------------------------

    public function test_total_task_count_is_correct(): void
    {
        Task::factory()->count(5)->create();

        $response = $this->get(route('dashboard'));

        $response->assertViewHas('totalTasks', 5);
    }

    public function test_completed_task_count_is_correct(): void
    {
        Task::factory()->count(3)->create(['status' => 'completed']);
        Task::factory()->count(2)->create(['status' => 'pending']);

        $response = $this->get(route('dashboard'));

        $response->assertViewHas('completedTasks', 3);
    }

    public function test_pending_task_count_is_correct(): void
    {
        Task::factory()->count(4)->create(['status' => 'pending']);
        Task::factory()->count(1)->create(['status' => 'in_progress']);

        $response = $this->get(route('dashboard'));

        $response->assertViewHas('pendingTasks', 4);
    }

    public function test_overdue_count_excludes_completed_but_includes_cancelled(): void
    {
        // Overdue = due_date in the past AND status != completed
        Task::factory()->create(['due_date' => now()->subDay(), 'status' => 'pending']);    // overdue
        Task::factory()->create(['due_date' => now()->subDay(), 'status' => 'cancelled']);  // overdue (intentional — see DashboardController comment)
        Task::factory()->create(['due_date' => now()->subDay(), 'status' => 'completed']);  // NOT overdue
        Task::factory()->create(['due_date' => now()->addDay(), 'status' => 'pending']);    // not yet due

        $response = $this->get(route('dashboard'));

        $response->assertViewHas('overdueTasks', 2);
    }

    // -------------------------------------------------------------------------
    // my tasks
    // -------------------------------------------------------------------------

    public function test_my_tasks_only_shows_authenticated_users_tasks(): void
    {
        $me    = User::factory()->create();
        $other = User::factory()->create();

        Task::factory()->create(['assigned_to' => $me->id,    'status' => 'pending']);
        Task::factory()->create(['assigned_to' => $other->id, 'status' => 'pending']);

        $response = $this->actingAs($me)->get(route('dashboard'));

        $myTasks = $response->viewData('myTasks');
        $this->assertCount(1, $myTasks);
        $this->assertEquals($me->id, $myTasks->first()->assigned_to);
    }

    public function test_my_tasks_excludes_completed_and_cancelled_statuses(): void
    {
        $user = User::factory()->create();

        Task::factory()->create(['assigned_to' => $user->id, 'status' => 'pending']);
        Task::factory()->create(['assigned_to' => $user->id, 'status' => 'in_progress']);
        Task::factory()->create(['assigned_to' => $user->id, 'status' => 'completed']);
        Task::factory()->create(['assigned_to' => $user->id, 'status' => 'cancelled']);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $myTasks = $response->viewData('myTasks');
        $this->assertCount(2, $myTasks);
        $this->assertTrue($myTasks->every(fn ($t) => in_array($t->status, ['pending', 'in_progress'])));
    }

    public function test_my_tasks_is_empty_for_unauthenticated_visitors(): void
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['assigned_to' => $user->id, 'status' => 'pending']);

        // auth()->id() returns null for guests, so the query matches no rows
        $response = $this->get(route('dashboard'));

        $response->assertViewHas('myTasks', fn ($tasks) => $tasks->isEmpty());
    }
}

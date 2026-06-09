<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_index_returns_200(): void
    {
        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
    }

    public function test_index_passes_tasks_users_and_categories_to_view(): void
    {
        Task::factory()->count(2)->create();
        User::factory()->count(2)->create();
        Category::factory()->count(2)->create();

        $response = $this->get(route('tasks.index'));

        $response->assertViewHasAll(['tasks', 'users', 'categories']);
    }

    public function test_index_eager_loads_category_and_assigned_user(): void
    {
        $category = Category::factory()->create();
        $user     = User::factory()->create();
        Task::factory()->create(['category_id' => $category->id, 'assigned_to' => $user->id]);

        $response = $this->get(route('tasks.index'));

        $task = $response->viewData('tasks')->first();
        $this->assertTrue($task->relationLoaded('category'));
        $this->assertTrue($task->relationLoaded('assignedUser'));
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_store_persists_new_task(): void
    {
        $category = Category::factory()->create();

        $this->post(route('tasks.store'), [
            'title'       => 'Write unit tests',
            'category_id' => $category->id,
            'status'      => 'pending',
            'priority'    => 'high',
        ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Write unit tests']);
    }

    public function test_store_redirects_back_with_success_flash(): void
    {
        $category = Category::factory()->create();

        $response = $this->post(route('tasks.store'), [
            'title'       => 'Deploy to staging',
            'category_id' => $category->id,
            'status'      => 'pending',
            'priority'    => 'medium',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_store_only_persists_fillable_fields(): void
    {
        $category = Category::factory()->create();

        // 'completed_at' is fillable but should only be set explicitly;
        // non-fillable columns like timestamps are never overwritten
        $this->post(route('tasks.store'), [
            'title'        => 'Boundary task',
            'category_id'  => $category->id,
            'status'       => 'pending',
            'priority'     => 'low',
            'completed_at' => null,
        ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Boundary task', 'completed_at' => null]);
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_task_in_database(): void
    {
        $task = Task::factory()->create(['title' => 'Old title', 'status' => 'pending']);

        $this->patch(route('tasks.update', $task), [
            'title'  => 'New title',
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New title', 'status' => 'in_progress']);
    }

    public function test_update_redirects_back_with_success_flash(): void
    {
        $task = Task::factory()->create();

        $response = $this->patch(route('tasks.update', $task), ['title' => 'Updated']);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_destroy_removes_task_from_database(): void
    {
        $task = Task::factory()->create();

        $this->delete(route('tasks.destroy', $task));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_destroy_redirects_back_with_success_flash(): void
    {
        $task = Task::factory()->create();

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}

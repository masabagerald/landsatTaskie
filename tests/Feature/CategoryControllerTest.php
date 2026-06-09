<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_index_returns_200(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);
    }

    public function test_index_passes_all_categories_to_view(): void
    {
        Category::factory()->count(3)->create();

        $response = $this->get(route('categories.index'));

        $response->assertViewHas('categories', fn ($cats) => $cats->count() === 3);
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_store_persists_new_category(): void
    {
        $this->post(route('categories.store'), [
            'name'        => 'Backend',
            'description' => 'Server-side tasks',
        ]);

        $this->assertDatabaseHas('categories', ['name' => 'Backend']);
    }

    public function test_store_redirects_back_with_success_flash(): void
    {
        $response = $this->post(route('categories.store'), ['name' => 'Design']);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_category_in_database(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->patch(route('categories.update', $category), [
            'name'        => 'New Name',
            'description' => 'Updated description',
        ]);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
        $this->assertDatabaseMissing('categories', ['name' => 'Old Name']);
    }

    public function test_update_redirects_back_with_success_flash(): void
    {
        $category = Category::factory()->create();

        $response = $this->patch(route('categories.update', $category), [
            'name' => 'Updated',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_destroy_removes_category_from_database(): void
    {
        $category = Category::factory()->create();

        $this->delete(route('categories.destroy', $category));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_destroy_cascades_to_tasks(): void
    {
        $category = Category::factory()->create();
        Task::factory()->create(['category_id' => $category->id]);

        $this->delete(route('categories.destroy', $category));

        // Foreign key is defined as cascadeOnDelete, so orphaned tasks must not remain
        $this->assertDatabaseEmpty('tasks');
    }
}

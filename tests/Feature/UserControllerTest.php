<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // index
    // -------------------------------------------------------------------------

    public function test_index_returns_200(): void
    {
        $response = $this->get(route('users.index'));

        $response->assertStatus(200);
    }

    public function test_index_passes_all_users_to_view(): void
    {
        User::factory()->count(3)->create();

        $response = $this->get(route('users.index'));

        $response->assertViewHas('users', fn ($users) => $users->count() === 3);
    }

    // -------------------------------------------------------------------------
    // store
    // -------------------------------------------------------------------------

    public function test_store_persists_new_user(): void
    {
        $this->post(route('users.store'), [
            'name'  => 'Alice Smith',
            'email' => 'alice@example.com',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
    }

    public function test_store_assigns_default_password_password123(): void
    {
        // New users created by an admin always start with 'password123' and must change it
        $this->post(route('users.store'), [
            'name'  => 'Bob Jones',
            'email' => 'bob@example.com',
        ]);

        $user = User::where('email', 'bob@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_store_redirects_back_with_success_flash(): void
    {
        $response = $this->post(route('users.store'), [
            'name'  => 'Carol White',
            'email' => 'carol@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    // -------------------------------------------------------------------------
    // update
    // -------------------------------------------------------------------------

    public function test_update_changes_name_and_email(): void
    {
        $user = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

        $this->patch(route('users.update', $user), [
            'name'  => 'New Name',
            'email' => 'new@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'New Name',
            'email' => 'new@example.com',
        ]);
    }

    public function test_update_does_not_change_password(): void
    {
        $user = User::factory()->create();
        $originalHash = $user->password;

        $this->patch(route('users.update', $user), [
            'name'  => 'Updated',
            'email' => $user->email,
        ]);

        // UserController::update only touches name and email
        $this->assertEquals($originalHash, $user->fresh()->password);
    }

    public function test_update_redirects_back_with_success_flash(): void
    {
        $user = User::factory()->create();

        $response = $this->patch(route('users.update', $user), [
            'name'  => 'Updated',
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    // -------------------------------------------------------------------------
    // destroy
    // -------------------------------------------------------------------------

    public function test_destroy_removes_user_from_database(): void
    {
        $user = User::factory()->create();

        $this->delete(route('users.destroy', $user));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_destroy_nullifies_tasks_assigned_to_user(): void
    {
        // The tasks.assigned_to FK is SET NULL on delete, not cascade
        $user = User::factory()->create();
        \App\Models\Task::factory()->create(['assigned_to' => $user->id]);

        $this->delete(route('users.destroy', $user));

        $this->assertDatabaseHas('tasks', ['assigned_to' => null]);
    }

    public function test_destroy_redirects_back_with_success_flash(): void
    {
        $user = User::factory()->create();

        $response = $this->delete(route('users.destroy', $user));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}

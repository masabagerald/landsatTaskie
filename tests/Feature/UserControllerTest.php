<?php

namespace Tests\Feature;

use App\Models\Task;
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

    public function test_index_passes_total_tasks_count(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        Task::factory()->count(3)->create(['assigned_to' => $userA->id]);
        Task::factory()->count(2)->create(['assigned_to' => $userB->id]);

        $response = $this->get(route('users.index'));

        $response->assertViewHas('totalTasks', 5);
    }

    // -------------------------------------------------------------------------
    // store — validation
    // -------------------------------------------------------------------------

    public function test_store_requires_name(): void
    {
        $response = $this->post(route('users.store'), ['email' => 'a@b.com']);

        $response->assertSessionHasErrors('name');
    }

    public function test_store_requires_valid_email(): void
    {
        $response = $this->post(route('users.store'), ['name' => 'X', 'email' => 'not-an-email']);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post(route('users.store'), [
            'name'  => 'Duplicate',
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_store_persists_new_user(): void
    {
        $this->post(route('users.store'), [
            'name'  => 'Alice Smith',
            'email' => 'alice@example.com',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'alice@example.com']);
    }

    public function test_store_assigns_default_password_when_none_given(): void
    {
        // New users created by an admin always start with 'password123' and must change it
        $this->post(route('users.store'), [
            'name'  => 'Bob Jones',
            'email' => 'bob@example.com',
        ]);

        $user = User::where('email', 'bob@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_store_uses_custom_password_when_provided(): void
    {
        $this->post(route('users.store'), [
            'name'                  => 'Carol White',
            'email'                 => 'carol@example.com',
            'password'              => 'secret99',
            'password_confirmation' => 'secret99',
        ]);

        $user = User::where('email', 'carol@example.com')->first();
        $this->assertTrue(Hash::check('secret99', $user->password));
    }

    public function test_store_rejects_mismatched_password_confirmation(): void
    {
        $response = $this->post(route('users.store'), [
            'name'                  => 'Dave',
            'email'                 => 'dave@example.com',
            'password'              => 'secret99',
            'password_confirmation' => 'wrong',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_store_redirects_back_with_success_flash(): void
    {
        $response = $this->post(route('users.store'), [
            'name'  => 'Eve',
            'email' => 'eve@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    // -------------------------------------------------------------------------
    // update — validation
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

    public function test_update_does_not_change_password_when_blank(): void
    {
        $user = User::factory()->create();
        $originalHash = $user->password;

        $this->patch(route('users.update', $user), [
            'name'  => 'Updated',
            'email' => $user->email,
        ]);

        $this->assertEquals($originalHash, $user->fresh()->password);
    }

    public function test_update_changes_password_when_provided(): void
    {
        $user = User::factory()->create();

        $this->patch(route('users.update', $user), [
            'name'                  => $user->name,
            'email'                 => $user->email,
            'password'              => 'newpassword1',
            'password_confirmation' => 'newpassword1',
        ]);

        $this->assertTrue(Hash::check('newpassword1', $user->fresh()->password));
    }

    public function test_update_allows_keeping_own_email(): void
    {
        $user = User::factory()->create(['email' => 'same@example.com']);

        $response = $this->patch(route('users.update', $user), [
            'name'  => 'Updated Name',
            'email' => 'same@example.com', // same email — should not fail uniqueness
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_update_rejects_email_already_taken_by_another_user(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create();

        $response = $this->patch(route('users.update', $user), [
            'name'  => 'X',
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors('email');
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
        // tasks.assigned_to FK is SET NULL on delete, not cascade
        $user = User::factory()->create();
        Task::factory()->create(['assigned_to' => $user->id]);

        $this->delete(route('users.destroy', $user));

        $this->assertDatabaseHas('tasks', ['assigned_to' => null]);
    }

    public function test_destroy_prevents_self_deletion(): void
    {
        $me = User::factory()->create();

        $response = $this->actingAs($me)->delete(route('users.destroy', $me));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $me->id]);
    }

    public function test_destroy_redirects_back_with_success_flash(): void
    {
        $user = User::factory()->create();

        $response = $this->delete(route('users.destroy', $user));

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}

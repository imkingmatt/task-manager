<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = 'user'): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_a_user_can_create_a_task(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Write tests',
            'description' => 'Cover the main flows',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Write tests',
            'user_id' => $user->id,
        ]);
    }

    public function test_a_user_can_view_only_their_own_tasks_on_the_index_page(): void
    {
        $user = $this->makeUser();
        $otherUser = $this->makeUser();

        Task::factory()->create(['user_id' => $user->id, 'title' => 'Mine']);
        Task::factory()->create(['user_id' => $otherUser->id, 'title' => 'Not mine']);

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertSee('Mine');
        $response->assertDontSee('Not mine');
    }

    public function test_a_user_can_update_their_own_task(): void
    {
        $user = $this->makeUser();
        $task = Task::factory()->create(['user_id' => $user->id, 'title' => 'Old title']);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'New title',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New title']);
    }

    public function test_a_user_can_delete_their_own_task(): void
    {
        $user = $this->makeUser();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_a_user_gets_a_403_editing_another_users_task(): void
    {
        $user = $this->makeUser();
        $otherUser = $this->makeUser();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get(route('tasks.edit', $task));

        $response->assertForbidden();
    }

    public function test_a_user_gets_a_403_deleting_another_users_task(): void
    {
        $user = $this->makeUser();
        $otherUser = $this->makeUser();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertForbidden();
        $this->assertDatabaseHas('tasks', ['id' => $task->id]); // still exists
    }

    public function test_a_non_admin_gets_a_403_visiting_the_admin_task_list(): void
    {
        $user = $this->makeUser('user');

        $response = $this->actingAs($user)->get(route('admin.tasks.index'));

        $response->assertForbidden();
    }

    public function test_an_admin_can_view_every_users_tasks_on_the_admin_index_page(): void
    {
        $admin = $this->makeUser('admin');
        $userA = $this->makeUser();
        $userB = $this->makeUser();

        Task::factory()->create(['user_id' => $userA->id, 'title' => 'Task A']);
        Task::factory()->create(['user_id' => $userB->id, 'title' => 'Task B']);

        $response = $this->actingAs($admin)->get(route('admin.tasks.index'));

        $response->assertSee('Task A');
        $response->assertSee('Task B');
    }

    public function test_an_admin_can_update_any_users_task(): void
    {
        $admin = $this->makeUser('admin');
        $owner = $this->makeUser();
        $task = Task::factory()->create(['user_id' => $owner->id, 'title' => 'Original']);

        $response = $this->actingAs($admin)->put(route('admin.tasks.update', $task), [
            'title' => 'Changed by admin',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('admin.tasks.index'));
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Changed by admin']);
    }

    public function test_an_admin_can_delete_any_users_task(): void
    {
        $admin = $this->makeUser('admin');
        $owner = $this->makeUser();
        $task = Task::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($admin)->delete(route('admin.tasks.destroy', $task));

        $response->assertRedirect(route('admin.tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_an_admins_own_my_tasks_page_shows_only_their_own_tasks(): void
    {
        // Regression test for the bug where an admin's personal task list
        // leaked every user's tasks because the service didn't distinguish
        // "admin visiting their own list" from "admin visiting the admin list."
        $admin = $this->makeUser('admin');
        $otherUser = $this->makeUser();

        Task::factory()->create(['user_id' => $admin->id, 'title' => 'Admin own task']);
        Task::factory()->create(['user_id' => $otherUser->id, 'title' => 'Someone elses task']);

        $response = $this->actingAs($admin)->get(route('tasks.index'));

        $response->assertSee('Admin own task');
        $response->assertDontSee('Someone elses task');
    }
}
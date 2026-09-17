<?php

namespace Tests\Feature\Api\V1;

use App\Models\Todo;
use App\Models\TodoCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TodoCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_categories_include_owned_and_assigned_task_categories(): void
    {
        $user = User::factory()->create();
        $taskOwner = User::factory()->create();

        $ownedCategory = TodoCategory::create([
            'user_id' => $user->id,
            'name' => 'Personal',
            'color' => '#6366f1',
        ]);
        $assignedCategory = TodoCategory::create([
            'user_id' => $taskOwner->id,
            'name' => 'Work',
            'color' => '#14b8a6',
        ]);

        Todo::create([
            'user_id' => $taskOwner->id,
            'assigned_to' => $user->id,
            'todo_category_id' => $assignedCategory->id,
            'title' => 'Prepare report',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/todo-categories');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(4, 'data')
            ->assertJsonFragment(['id' => $ownedCategory->id])
            ->assertJsonFragment(['id' => $assignedCategory->id])
            ->assertJsonFragment(['name' => 'Home', 'is_default' => true])
            ->assertJsonFragment(['name' => 'Market', 'is_default' => true]);
    }

    public function test_todo_can_be_created_with_a_selected_category(): void
    {
        $user = User::factory()->create();
        $category = TodoCategory::create([
            'user_id' => $user->id,
            'name' => 'Errands',
            'color' => '#f59e0b',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/todos', [
            'title' => 'Buy groceries',
            'todo_category_id' => $category->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.todo_category_id', $category->id);

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => 'Buy groceries',
            'todo_category_id' => $category->id,
        ]);
    }

    public function test_index_creates_non_deletable_default_categories(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/todo-categories');

        $response->assertOk()
            ->assertJsonFragment(['name' => 'Home', 'is_default' => true])
            ->assertJsonFragment(['name' => 'Market', 'is_default' => true]);

        $home = TodoCategory::where('user_id', $user->id)->where('name', 'Home')->firstOrFail();
        $this->deleteJson("/api/v1/todo-categories/{$home->id}")
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Default categories cannot be deleted.');

        $this->assertDatabaseHas('todo_categories', ['id' => $home->id]);
    }
}

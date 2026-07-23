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
            ->assertJsonCount(2, 'data')
            ->assertJsonFragment(['id' => $ownedCategory->id])
            ->assertJsonFragment(['id' => $assignedCategory->id]);
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
}

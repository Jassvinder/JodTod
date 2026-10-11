<?php

namespace Tests\Feature\Api\V1;

use App\Models\RememberItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RememberItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_item_can_be_created_with_a_first_entry(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/remember-items', [
            'name' => 'Gas Cylinder',
            'done_on' => '2026-07-10',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Gas Cylinder')
            ->assertJsonPath('data.entries_count', 1)
            ->assertJsonPath('data.last_done_on', '2026-07-10')
            ->assertJsonPath('data.avg_gap_days', null)
            ->assertJsonPath('data.entries.0.done_on', '2026-07-10');
    }

    public function test_list_shows_last_date_and_average_gap(): void
    {
        $user = User::factory()->create();
        $item = RememberItem::create(['user_id' => $user->id, 'name' => 'Gas Cylinder']);
        $item->entries()->createMany([
            ['done_on' => '2026-07-10'],
            ['done_on' => '2026-08-21'],
            ['done_on' => '2026-10-02'],
        ]);
        RememberItem::create(['user_id' => User::factory()->create()->id, 'name' => 'Not mine']);

        Sanctum::actingAs($user);

        $this->getJson('/api/v1/remember-items')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.entries_count', 3)
            ->assertJsonPath('data.0.last_done_on', '2026-10-02')
            ->assertJsonPath('data.0.avg_gap_days', 42);

        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('data.rememberStats.total', 1);
    }

    public function test_entries_can_be_added_and_deleted(): void
    {
        $user = User::factory()->create();
        $item = RememberItem::create(['user_id' => $user->id, 'name' => 'Remote Battery']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/remember-items/{$item->id}/entries", [
            'done_on' => '2026-06-15',
            'note' => 'AA batteries',
        ]);

        $response->assertCreated()->assertJsonPath('data.entries_count', 1);
        $entryId = $response->json('data.entries.0.id');

        $this->deleteJson("/api/v1/remember-items/{$item->id}/entries/{$entryId}")
            ->assertOk()
            ->assertJsonPath('data.entries_count', 0)
            ->assertJsonPath('data.last_done_on', null);
    }

    public function test_future_dates_are_rejected(): void
    {
        $user = User::factory()->create();
        $item = RememberItem::create(['user_id' => $user->id, 'name' => 'Water Filter']);
        Sanctum::actingAs($user);

        $this->postJson("/api/v1/remember-items/{$item->id}/entries", [
            'done_on' => now()->addDay()->toDateString(),
        ])->assertUnprocessable();
    }

    public function test_other_users_cannot_access_an_item(): void
    {
        $owner = User::factory()->create();
        $item = RememberItem::create(['user_id' => $owner->id, 'name' => 'Gas Cylinder']);
        Sanctum::actingAs(User::factory()->create());

        $this->getJson("/api/v1/remember-items/{$item->id}")->assertForbidden();
        $this->postJson("/api/v1/remember-items/{$item->id}/entries", ['done_on' => '2026-01-01'])->assertForbidden();
        $this->deleteJson("/api/v1/remember-items/{$item->id}")->assertForbidden();
    }

    public function test_entry_must_belong_to_the_item(): void
    {
        $user = User::factory()->create();
        $first = RememberItem::create(['user_id' => $user->id, 'name' => 'First']);
        $second = RememberItem::create(['user_id' => $user->id, 'name' => 'Second']);
        $entry = $second->entries()->create(['done_on' => '2026-01-01']);
        Sanctum::actingAs($user);

        $this->deleteJson("/api/v1/remember-items/{$first->id}/entries/{$entry->id}")->assertNotFound();
        $this->assertDatabaseHas('remember_entries', ['id' => $entry->id]);
    }
}

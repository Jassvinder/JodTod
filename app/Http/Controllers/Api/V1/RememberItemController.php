<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RememberEntry;
use App\Models\RememberItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RememberItemController extends Controller
{
    public function index(): JsonResponse
    {
        $items = $this->withEntryStats(RememberItem::where('user_id', Auth::id()))
            ->orderBy('name')
            ->get()
            ->map(fn (RememberItem $item) => $this->present($item));

        return $this->success($items);
    }

    public function show(RememberItem $rememberItem): JsonResponse
    {
        $this->authorizeItem($rememberItem);

        return $this->success($this->presentWithEntries($rememberItem));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'note' => 'nullable|string|max:255',
            'done_on' => 'nullable|date|before_or_equal:today',
        ]);

        $item = DB::transaction(function () use ($validated) {
            $item = RememberItem::create([
                'user_id' => Auth::id(),
                'name' => $validated['name'],
                'note' => $validated['note'] ?? null,
            ]);

            if (! empty($validated['done_on'])) {
                $item->entries()->create(['done_on' => $validated['done_on']]);
            }

            return $item;
        });

        return $this->created($this->presentWithEntries($item), 'Item created successfully.');
    }

    public function update(Request $request, RememberItem $rememberItem): JsonResponse
    {
        $this->authorizeItem($rememberItem);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'note' => 'nullable|string|max:255',
        ]);

        $rememberItem->update($validated);

        return $this->success($this->presentWithEntries($rememberItem), 'Item updated successfully.');
    }

    public function destroy(RememberItem $rememberItem): JsonResponse
    {
        $this->authorizeItem($rememberItem);

        $rememberItem->delete();

        return $this->success(null, 'Item deleted successfully.');
    }

    public function storeEntry(Request $request, RememberItem $rememberItem): JsonResponse
    {
        $this->authorizeItem($rememberItem);

        $validated = $request->validate([
            'done_on' => 'required|date|before_or_equal:today',
            'note' => 'nullable|string|max:255',
        ]);

        $rememberItem->entries()->create($validated);

        return $this->created($this->presentWithEntries($rememberItem), 'Entry added successfully.');
    }

    public function destroyEntry(RememberItem $rememberItem, RememberEntry $rememberEntry): JsonResponse
    {
        $this->authorizeItem($rememberItem);

        if ($rememberEntry->remember_item_id !== $rememberItem->id) {
            abort(response()->json([
                'success' => false,
                'message' => 'Not Found',
            ], 404));
        }

        $rememberEntry->delete();

        return $this->success($this->presentWithEntries($rememberItem), 'Entry deleted successfully.');
    }

    private function withEntryStats(Builder $query): Builder
    {
        return $query->withCount('entries')
            ->withMin('entries', 'done_on')
            ->withMax('entries', 'done_on');
    }

    /**
     * Item summary: last done date and the average number of days between entries.
     */
    private function present(RememberItem $item): array
    {
        $count = (int) $item->entries_count;
        $first = $item->entries_min_done_on ? Carbon::parse($item->entries_min_done_on) : null;
        $last = $item->entries_max_done_on ? Carbon::parse($item->entries_max_done_on) : null;

        return [
            'id' => $item->id,
            'name' => $item->name,
            'note' => $item->note,
            'entries_count' => $count,
            'last_done_on' => $last?->toDateString(),
            'avg_gap_days' => $count > 1 ? (int) round($first->diffInDays($last) / ($count - 1)) : null,
            'created_at' => $item->created_at,
        ];
    }

    private function presentWithEntries(RememberItem $item): array
    {
        $item = $this->withEntryStats(RememberItem::whereKey($item->id))->firstOrFail();

        return [
            ...$this->present($item),
            'entries' => $item->entries()
                ->orderByDesc('done_on')
                ->orderByDesc('id')
                ->get(['id', 'done_on', 'note']),
        ];
    }

    private function authorizeItem(RememberItem $item): void
    {
        if ($item->user_id !== Auth::id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403));
        }
    }
}

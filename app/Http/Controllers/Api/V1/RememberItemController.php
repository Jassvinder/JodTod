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
        $items = RememberItem::where('user_id', Auth::id())
            ->withEntryStats()
            ->orderBy('name')
            ->get()
            ->map(fn (RememberItem $item) => $item->toSummary());

        return $this->success($items);
    }

    public function show(RememberItem $rememberItem): JsonResponse
    {
        $this->authorizeItem($rememberItem);

        return $this->success($rememberItem->toDetail());
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

        return $this->created($item->toDetail(), 'Item created successfully.');
    }

    public function update(Request $request, RememberItem $rememberItem): JsonResponse
    {
        $this->authorizeItem($rememberItem);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'note' => 'nullable|string|max:255',
        ]);

        $rememberItem->update($validated);

        return $this->success($rememberItem->toDetail(), 'Item updated successfully.');
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

        return $this->created($rememberItem->toDetail(), 'Entry added successfully.');
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

        return $this->success($rememberItem->toDetail(), 'Entry deleted successfully.');
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

<?php

namespace App\Http\Controllers;

use App\Models\RememberEntry;
use App\Models\RememberItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RememberController extends Controller
{
    public function index()
    {
        return $this->render();
    }

    public function show(RememberItem $rememberItem)
    {
        $this->authorizeItem($rememberItem);

        return $this->render($rememberItem);
    }

    public function store(Request $request)
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

        return redirect()->route('remember.show', $item)->with('success', 'Item added.');
    }

    public function update(Request $request, RememberItem $rememberItem)
    {
        $this->authorizeItem($rememberItem);

        $rememberItem->update($request->validate([
            'name' => 'required|string|max:100',
            'note' => 'nullable|string|max:255',
        ]));

        return redirect()->back()->with('success', 'Item updated.');
    }

    public function destroy(RememberItem $rememberItem)
    {
        $this->authorizeItem($rememberItem);

        $rememberItem->delete();

        return redirect()->route('remember.index')->with('success', 'Item deleted.');
    }

    public function storeEntry(Request $request, RememberItem $rememberItem)
    {
        $this->authorizeItem($rememberItem);

        $rememberItem->entries()->create($request->validate([
            'done_on' => 'required|date|before_or_equal:today',
            'note' => 'nullable|string|max:255',
        ]));

        return redirect()->back()->with('success', 'Date added.');
    }

    public function destroyEntry(RememberItem $rememberItem, RememberEntry $rememberEntry)
    {
        $this->authorizeItem($rememberItem);

        if ($rememberEntry->remember_item_id !== $rememberItem->id) {
            abort(404);
        }

        $rememberEntry->delete();

        return redirect()->back()->with('success', 'Date deleted.');
    }

    /**
     * One page for the list and the selected item, so wide screens can show both side by side.
     */
    private function render(?RememberItem $selected = null)
    {
        $items = RememberItem::where('user_id', Auth::id())
            ->withEntryStats()
            ->orderBy('name')
            ->get()
            ->map(fn (RememberItem $item) => $item->toSummary());

        return Inertia::render('Remember/Index', [
            'items' => $items,
            'selected' => $selected?->toDetail(),
        ]);
    }

    private function authorizeItem(RememberItem $item): void
    {
        if ($item->user_id !== Auth::id()) {
            abort(403);
        }
    }
}

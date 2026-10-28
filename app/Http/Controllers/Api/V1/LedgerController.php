<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
use App\Models\LedgerPerson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Lend & Borrow: money given to and received from people, with running balances.
 */
class LedgerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $people = LedgerPerson::where('user_id', Auth::id())
            ->withLedgerTotals()
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->search . '%'))
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (LedgerPerson $person) => $person->toSummary());

        return $this->success([
            'people' => $people,
            'totals' => LedgerPerson::totalsFor(Auth::id()),
        ]);
    }

    public function show(LedgerPerson $ledgerPerson): JsonResponse
    {
        $this->authorizePerson($ledgerPerson);

        return $this->success($ledgerPerson->toDetail());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate(LedgerPerson::RULES);

        $person = LedgerPerson::create([
            ...$validated,
            'user_id' => Auth::id(),
        ]);

        return $this->created($person->toDetail(), 'Person added successfully.');
    }

    public function update(Request $request, LedgerPerson $ledgerPerson): JsonResponse
    {
        $this->authorizePerson($ledgerPerson);

        $validated = $request->validate(LedgerPerson::RULES);

        $ledgerPerson->update($validated);

        return $this->success($ledgerPerson->toDetail(), 'Person updated successfully.');
    }

    public function destroy(LedgerPerson $ledgerPerson): JsonResponse
    {
        $this->authorizePerson($ledgerPerson);

        // Delete entries explicitly: MyISAM tables ignore the foreign key cascade.
        DB::transaction(function () use ($ledgerPerson) {
            $ledgerPerson->entries()->delete();
            $ledgerPerson->delete();
        });

        return $this->success(null, 'Person and all entries deleted successfully.');
    }

    public function storeEntry(Request $request, LedgerPerson $ledgerPerson): JsonResponse
    {
        $this->authorizePerson($ledgerPerson);

        $validated = $this->validateEntry($request);

        DB::transaction(function () use ($ledgerPerson, $validated) {
            $ledgerPerson->entries()->create($validated);
            // Most recently active people appear first in the list.
            $ledgerPerson->touch();
        });

        return $this->created($ledgerPerson->toDetail(), 'Entry added successfully.');
    }

    public function updateEntry(Request $request, LedgerPerson $ledgerPerson, LedgerEntry $ledgerEntry): JsonResponse
    {
        $this->authorizePerson($ledgerPerson);
        $this->ensureEntryBelongs($ledgerPerson, $ledgerEntry);

        $ledgerEntry->update($this->validateEntry($request));

        return $this->success($ledgerPerson->toDetail(), 'Entry updated successfully.');
    }

    public function destroyEntry(LedgerPerson $ledgerPerson, LedgerEntry $ledgerEntry): JsonResponse
    {
        $this->authorizePerson($ledgerPerson);
        $this->ensureEntryBelongs($ledgerPerson, $ledgerEntry);

        $ledgerEntry->delete();

        return $this->success($ledgerPerson->toDetail(), 'Entry deleted successfully.');
    }

    private function validateEntry(Request $request): array
    {
        return $request->validate(LedgerEntry::RULES);
    }

    private function ensureEntryBelongs(LedgerPerson $person, LedgerEntry $entry): void
    {
        if ($entry->ledger_person_id !== $person->id) {
            abort(response()->json([
                'success' => false,
                'message' => 'Entry not found.',
            ], 404));
        }
    }

    private function authorizePerson(LedgerPerson $person): void
    {
        if ($person->user_id !== Auth::id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'You do not have access to this record.',
            ], 403));
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\LedgerPerson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * Give & Take (web): money given to and received from people, with running balances.
 */
class LedgerController extends Controller
{
    public function index()
    {
        return $this->render();
    }

    public function show(LedgerPerson $ledgerPerson)
    {
        $this->authorizePerson($ledgerPerson);

        return $this->render($ledgerPerson);
    }

    public function store(Request $request)
    {
        $person = LedgerPerson::create([
            ...$request->validate(LedgerPerson::RULES),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('ledger.show', $person)->with('success', 'Person added. Now add what you gave or got.');
    }

    public function update(Request $request, LedgerPerson $ledgerPerson)
    {
        $this->authorizePerson($ledgerPerson);

        $ledgerPerson->update($request->validate(LedgerPerson::RULES));

        return redirect()->back()->with('success', 'Person updated.');
    }

    public function destroy(LedgerPerson $ledgerPerson)
    {
        $this->authorizePerson($ledgerPerson);

        // Delete entries explicitly: MyISAM tables ignore the foreign key cascade.
        DB::transaction(function () use ($ledgerPerson) {
            $ledgerPerson->entries()->delete();
            $ledgerPerson->delete();
        });

        return redirect()->route('ledger.index')->with('success', 'Person and all entries deleted.');
    }

    public function storeEntry(Request $request, LedgerPerson $ledgerPerson)
    {
        $this->authorizePerson($ledgerPerson);

        $validated = $request->validate(LedgerEntry::RULES);

        DB::transaction(function () use ($ledgerPerson, $validated) {
            $ledgerPerson->entries()->create($validated);
            // Most recently active people appear first in the list.
            $ledgerPerson->touch();
        });

        return redirect()->back()->with('success', 'Entry added.');
    }

    public function updateEntry(Request $request, LedgerPerson $ledgerPerson, LedgerEntry $ledgerEntry)
    {
        $this->authorizePerson($ledgerPerson);
        $this->ensureEntryBelongs($ledgerPerson, $ledgerEntry);

        $ledgerEntry->update($request->validate(LedgerEntry::RULES));

        return redirect()->back()->with('success', 'Entry updated.');
    }

    public function destroyEntry(LedgerPerson $ledgerPerson, LedgerEntry $ledgerEntry)
    {
        $this->authorizePerson($ledgerPerson);
        $this->ensureEntryBelongs($ledgerPerson, $ledgerEntry);

        $ledgerEntry->delete();

        return redirect()->back()->with('success', 'Entry deleted.');
    }

    /**
     * One page for the list and the selected person, so wide screens can show both side by side.
     */
    private function render(?LedgerPerson $selected = null)
    {
        $people = LedgerPerson::where('user_id', Auth::id())
            ->withLedgerTotals()
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn (LedgerPerson $person) => $person->toSummary());

        return Inertia::render('Ledger/Index', [
            'people' => $people,
            'totals' => LedgerPerson::totalsFor(Auth::id()),
            'selected' => $selected?->toDetail(),
        ]);
    }

    private function ensureEntryBelongs(LedgerPerson $person, LedgerEntry $entry): void
    {
        if ($entry->ledger_person_id !== $person->id) {
            abort(404);
        }
    }

    private function authorizePerson(LedgerPerson $person): void
    {
        if ($person->user_id !== Auth::id()) {
            abort(403);
        }
    }
}

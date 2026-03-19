<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with('contactUser:id,name,email,phone,avatar')
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'user' => $c->contactUser,
                'created_at' => $c->created_at,
            ]);

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
        ]);
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 3) {
            return response()->json([]);
        }

        $existingContactIds = Contact::where('user_id', Auth::id())
            ->pluck('contact_user_id')
            ->push(Auth::id()) // exclude self
            ->toArray();

        $users = User::where(function ($query) use ($q) {
                $query->where('email', 'LIKE', "%{$q}%")
                    ->orWhere('phone', 'LIKE', "%{$q}%")
                    ->orWhere('name', 'LIKE', "%{$q}%");
            })
            ->whereNotIn('id', $existingContactIds)
            ->select('id', 'name', 'email', 'phone', 'avatar')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contact_user_id' => 'required|exists:users,id',
        ]);

        if ($validated['contact_user_id'] == Auth::id()) {
            return redirect()->back()->with('error', 'You cannot add yourself.');
        }

        $exists = Contact::where('user_id', Auth::id())
            ->where('contact_user_id', $validated['contact_user_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Already in your contacts.');
        }

        Contact::create([
            'user_id' => Auth::id(),
            'contact_user_id' => $validated['contact_user_id'],
        ]);

        return redirect()->back()->with('success', 'Contact added.');
    }

    public function destroy(Contact $contact)
    {
        if ($contact->user_id !== Auth::id()) {
            abort(403);
        }

        $contact->delete();

        return redirect()->back()->with('success', 'Contact removed.');
    }
}

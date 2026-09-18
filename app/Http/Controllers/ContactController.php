<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Notifications\ContactRequestReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::with('contactUser:id,name,email,friend_code,avatar')
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->latest()
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'user' => $c->contactUser,
                'created_at' => $c->created_at,
            ]);

        $requestCount = Contact::where('contact_user_id', Auth::id())
            ->where('status', 'pending')
            ->count();

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'requestCount' => $requestCount,
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

        $users = User::where('name', 'LIKE', "%{$q}%")
            ->whereNotIn('id', $existingContactIds)
            ->select('id', 'name', 'friend_code', 'avatar')
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

        $existingContact = Contact::where('user_id', Auth::id())
            ->where('contact_user_id', $validated['contact_user_id'])
            ->first();

        if ($existingContact?->status === 'approved') {
            return redirect()->back()->with('error', 'This user is already in your contacts.');
        }

        if ($existingContact?->status === 'pending') {
            return redirect()->back()->with('error', 'A contact request is already pending approval.');
        }

        $contact = Contact::create([
            'user_id' => Auth::id(),
            'contact_user_id' => $validated['contact_user_id'],
            'status' => 'pending',
        ]);

        $contact->contactUser->notify(new ContactRequestReceived($contact, Auth::user()));

        return redirect()->back()->with('success', 'Contact request sent for approval.');
    }

    public function requests()
    {
        $requests = Contact::with('user:id,name,friend_code,avatar')
            ->where('contact_user_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        return Inertia::render('Contacts/Requests', [
            'requests' => $requests,
        ]);
    }

    public function approve(Contact $contact)
    {
        if ($contact->contact_user_id !== Auth::id() || $contact->status !== 'pending') {
            abort(403);
        }

        $contact->update(['status' => 'approved']);

        Contact::updateOrCreate(
            ['user_id' => Auth::id(), 'contact_user_id' => $contact->user_id],
            ['status' => 'approved'],
        );

        return redirect()->back()->with('success', 'Contact request approved.');
    }

    public function reject(Contact $contact)
    {
        if ($contact->contact_user_id !== Auth::id() || $contact->status !== 'pending') {
            abort(403);
        }

        $contact->delete();

        return redirect()->back()->with('success', 'Contact request declined.');
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

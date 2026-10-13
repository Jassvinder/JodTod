<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Notifications\ContactRequestReceived;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Contact::with('contactUser:id,name,email,friend_code,avatar')
            ->where('user_id', Auth::id())
            ->where('status', 'approved')
            ->latest();

        // Search by contact user's name, email, or phone
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('contactUser', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $paginator = $query->paginate(20);

        return $this->paginated($paginator);
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->input('q', '');

        if (strlen($q) < 3) {
            return $this->success([]);
        }

        return $this->success(Contact::searchUsersFor(Auth::id(), $q));
    }

    public function sentRequests(): JsonResponse
    {
        $requests = Contact::with('contactUser:id,name,friend_code,avatar')
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        return $this->success($requests);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_user_id' => 'required|exists:users,id',
        ]);

        if ($validated['contact_user_id'] == Auth::id()) {
            return $this->error('You cannot add yourself as a contact.', 422);
        }

        $existingContact = Contact::where('user_id', Auth::id())
            ->where('contact_user_id', $validated['contact_user_id'])
            ->first();

        if ($existingContact?->status === 'approved') {
            return $this->error('This user is already in your contacts.', 409);
        }

        if ($existingContact?->status === 'pending') {
            return $this->error('A contact request is already pending approval.', 409);
        }

        $contact = Contact::create([
            'user_id' => Auth::id(),
            'contact_user_id' => $validated['contact_user_id'],
            'status' => 'pending',
        ]);

        $contact->load('contactUser:id,name,friend_code,avatar');

        $contact->contactUser->notify(new ContactRequestReceived($contact, Auth::user()));

        return $this->created($contact, 'Contact request sent for approval.');
    }

    public function requests(): JsonResponse
    {
        $requests = Contact::with('user:id,name,friend_code,avatar')
            ->where('contact_user_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        return $this->success($requests);
    }

    public function approve(Contact $contact): JsonResponse
    {
        if ($contact->contact_user_id !== Auth::id() || $contact->status !== 'pending') {
            return $this->forbidden('This contact request is not available.');
        }

        $contact->update(['status' => 'approved']);

        Contact::updateOrCreate(
            ['user_id' => Auth::id(), 'contact_user_id' => $contact->user_id],
            ['status' => 'approved'],
        );

        return $this->success(null, 'Contact request approved.');
    }

    public function reject(Contact $contact): JsonResponse
    {
        if ($contact->contact_user_id !== Auth::id() || $contact->status !== 'pending') {
            return $this->forbidden('This contact request is not available.');
        }

        $contact->delete();

        return $this->success(null, 'Contact request declined.');
    }

    public function destroy(Contact $contact): JsonResponse
    {
        if ($contact->user_id !== Auth::id()) {
            return $this->forbidden('You do not own this contact.');
        }

        $contact->delete();

        return $this->success(null, 'Contact removed successfully.');
    }
}

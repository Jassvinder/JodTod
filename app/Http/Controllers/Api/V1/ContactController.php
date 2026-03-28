<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Contact::with('contactUser:id,name,email,phone,avatar')
            ->where('user_id', Auth::id())
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

        if (strlen($q) < 2) {
            return $this->success([]);
        }

        $existingContactIds = Contact::where('user_id', Auth::id())
            ->pluck('contact_user_id')
            ->push(Auth::id())
            ->toArray();

        $users = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            })
            ->whereNotIn('id', $existingContactIds)
            ->select('id', 'name', 'email', 'phone', 'avatar')
            ->limit(20)
            ->get();

        return $this->success($users);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'contact_user_id' => 'required|exists:users,id',
        ]);

        if ($validated['contact_user_id'] == Auth::id()) {
            return $this->error('You cannot add yourself as a contact.', 422);
        }

        $exists = Contact::where('user_id', Auth::id())
            ->where('contact_user_id', $validated['contact_user_id'])
            ->exists();

        if ($exists) {
            return $this->error('This user is already in your contacts.', 409);
        }

        $contact = Contact::create([
            'user_id' => Auth::id(),
            'contact_user_id' => $validated['contact_user_id'],
        ]);

        $contact->load('contactUser:id,name,email,phone,avatar');

        return $this->created($contact, 'Contact added successfully.');
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

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response|\Illuminate\Http\JsonResponse
    {
        if ($this->wantsJson()) {
            return $this->success($request->user());
        }

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        if ($this->wantsJson()) {
            return $this->success($request->user(), 'Profile updated successfully.');
        }

        return Redirect::route('profile.edit');
    }

    /**
     * Update the user's avatar from a base64 image.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'string'],
        ]);

        $base64 = $request->input('avatar');

        // Strip data URI prefix if present (e.g. "data:image/png;base64,...")
        if (str_contains($base64, ',')) {
            $base64 = substr($base64, strpos($base64, ',') + 1);
        }

        $decoded = base64_decode($base64, true);

        if ($decoded === false) {
            return back()->withErrors(['avatar' => 'Invalid image data.']);
        }

        // Max 5MB check on decoded image
        if (strlen($decoded) > 5 * 1024 * 1024) {
            return back()->withErrors(['avatar' => 'Image must be less than 5MB.']);
        }

        // Convert to webp for smaller file size
        $user = $request->user();
        $path = "avatars/{$user->id}.webp";

        $image = imagecreatefromstring($decoded);
        if ($image === false) {
            return back()->withErrors(['avatar' => 'Could not process image.']);
        }

        ob_start();
        imagewebp($image, null, 80);
        $webpData = ob_get_clean();

        Storage::disk('public')->put($path, $webpData);

        $user->update(['avatar' => $path]);

        return response()->json([
            'avatar_url' => Storage::disk('public')->url($path),
        ]);
    }

    /**
     * Remove the user's avatar.
     */
    public function destroyAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return response()->json(['message' => 'Avatar removed.']);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if ($this->wantsJson()) {
            $user->tokens()->delete();
            $user->delete();

            return $this->success(null, 'Account deleted successfully.');
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

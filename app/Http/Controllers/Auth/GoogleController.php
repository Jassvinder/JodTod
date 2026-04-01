<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($user) {
                $updates = [];

                if (!$user->email_verified_at) {
                    $updates['email_verified_at'] = now();
                }

                if (!$user->google_id) {
                    $updates['google_id'] = $googleUser->id;
                    $updates['avatar'] = $user->avatar ?? $googleUser->avatar;
                }

                // Agar updates array khali nahi hai, toh sirf EK baar update chalao
                if (!empty($updates)) {
                    $user->update($updates);
                }
            } else {
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                    'email_verified_at' => now(),
                ]);
            }
            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard'));
        } catch (\Exception $e) {
            // Agar code expire ho gaya ya koi error aaya, toh wapas login pe bhej do
            return redirect('/login')->with('error', 'Google authentication failed. Please try again.');
        }
    }
}

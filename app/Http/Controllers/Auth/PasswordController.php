<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $rules = [
            'password' => ['required', Password::defaults(), 'confirmed'],
        ];

        if ($user->has_password) {
            $rules['current_password'] = ['required', 'current_password'];
        }

        // 2. Run Validation
        $validated = $request->validate($rules, [
            'current_password.current_password' => 'Current password is incorrect.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        if ($this->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $user->has_password
                    ? 'Password updated successfully.'
                    : 'Password set successfully.',
            ]);
        }

        return back();
    }
}

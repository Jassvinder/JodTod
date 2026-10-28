<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * A Team Builder rule was broken (wrong invite code, not your team, ...). The message is shown to the user:
 * as JSON for the mobile API, or as a flash error on the previous web page.
 */
class TeamBuilderException extends RuntimeException
{
    public function __construct(string $message, private int $status = 422)
    {
        parent::__construct($message);
    }

    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $this->getMessage()], $this->status);
        }

        return back()->with('error', $this->getMessage());
    }
}

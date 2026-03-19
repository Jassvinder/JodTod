<?php

namespace App\Http\Controllers;

use App\Traits\ApiResponse;

abstract class Controller
{
    use ApiResponse;

    protected function wantsJson(): bool
    {
        return request()->is('api/*') || request()->wantsJson();
    }
}

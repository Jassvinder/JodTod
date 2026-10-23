<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\SupportService;
use Illuminate\Http\JsonResponse;

class SupportController extends Controller
{
    public function show(): JsonResponse
    {
        return $this->success(SupportService::details());
    }
}

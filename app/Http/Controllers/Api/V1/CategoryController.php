<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::orderBy('name')->get();

        // Never hand the app an empty list: an empty table makes expenses impossible to add.
        if ($categories->isEmpty()) {
            Category::ensureDefaults();
            $categories = Category::orderBy('name')->get();
        }

        return $this->success($categories);
    }
}

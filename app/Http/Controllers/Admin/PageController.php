<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageController extends Controller
{
    public function index()
    {
        $pages = Page::orderBy('title')->get();

        return Inertia::render('Admin/Pages/Index', [
            'pages' => $pages,
        ]);
    }

    public function edit(Page $page)
    {
        return Inertia::render('Admin/Pages/Edit', [
            'page' => $page,
        ]);
    }

    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'is_published' => 'boolean',
        ]);

        $page->update($validated);

        return redirect()->route('admin.pages')
            ->with('success', 'Page updated successfully.');
    }

    public function uploadImage(Request $request)
    {
        $fileKey = $request->hasFile('upload') ? 'upload' : 'image';

        $request->validate([
            $fileKey => 'required|image|max:5120',
        ]);

        $file = $request->file($fileKey);
        $image = imagecreatefromstring(file_get_contents($file->getPathname()));

        if ($image === false) {
            return response()->json(['error' => 'Could not process image.'], 422);
        }

        $filename = 'uploads/' . uniqid() . '.webp';
        ob_start();
        imagewebp($image, null, 80);
        $webpData = ob_get_clean();

        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $webpData);

        return response()->json([
            'url' => '/storage/' . $filename,
        ]);
    }
}

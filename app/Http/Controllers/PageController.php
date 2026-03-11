<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    /**
     * Show a CMS page by slug, falling back to static Blade view.
     */
    private function showPage(string $slug, string $fallbackView, string $defaultTitle = '')
    {
        $page = Page::published()->where('slug', $slug)->first();

        if ($page) {
            return view('pages.public.cms', [
                'page' => $page,
            ]);
        }

        return view($fallbackView);
    }

    public function features()
    {
        return $this->showPage('features', 'pages.public.features');
    }

    public function about()
    {
        return $this->showPage('about', 'pages.public.about');
    }

    public function contact()
    {
        return $this->showPage('contact', 'pages.public.contact');
    }

    public function privacy()
    {
        return $this->showPage('privacy', 'pages.public.privacy');
    }

    public function terms()
    {
        return $this->showPage('terms', 'pages.public.terms');
    }

    public function splitter()
    {
        return view('pages.public.tools.splitter');
    }
}

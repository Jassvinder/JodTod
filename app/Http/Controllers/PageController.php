<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function features()
    {
        return view('pages.public.features');
    }

    public function about()
    {
        return view('pages.public.about');
    }

    public function contact()
    {
        return view('pages.public.contact');
    }

    public function privacy()
    {
        return view('pages.public.privacy');
    }

    public function terms()
    {
        return view('pages.public.terms');
    }

    public function splitter()
    {
        return view('pages.public.tools.splitter');
    }
}

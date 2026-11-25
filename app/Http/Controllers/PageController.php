<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function services(): View
    {
        return view('pages.services');
    }

    public function formation(): View
    {
        return view('pages.formation');
    }

    public function caseStudies(): View
    {
        return view('pages.etudes-de-cas');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function contactMerci(): View
    {
        return view('pages.contact-merci');
    }

    public function mentionsLegales(): View
    {
        return view('pages.mentions-legales');
    }

    public function politiqueConfidentialite(): View
    {
        return view('pages.politique-confidentialite');
    }

    public function cgv(): View
    {
        return view('pages.cgv');
    }
}

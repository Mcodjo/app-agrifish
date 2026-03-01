<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function services()
    {
        return view('pages.services');
    }

    public function formation()
    {
        return view('pages.formation');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactMerci()
    {
        return view('pages.contact-merci');
    }

    public function mentionsLegales()
    {
        return view('pages.mentions-legales');
    }

    public function politiqueConfidentialite()
    {
        return view('pages.politique-confidentialite');
    }

    public function caseStudies()
    {
        return view('pages.etudes-de-cas');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'seo' => [
                'title' => 'Accueil',
                'description' => 'AgriFish accompagne les projets agricoles et aquacoles en Afrique avec conseil, études techniques, réalisations et formation.',
                'url' => route('home'),
            ],
        ]);
    }

    public function about()
    {
        return view('pages.about', [
            'seo' => [
                'title' => 'À propos et équipe',
                'description' => 'Découvrez la mission AgriFish, notre équipe et notre approche de terrain au service des projets agricoles et aquacoles.',
                'url' => route('about'),
            ],
        ]);
    }

    public function services()
    {
        return view('pages.services', [
            'seo' => [
                'title' => 'Services',
                'description' => 'Agriculture, aquaculture et études techniques: des services structurés pour concevoir, lancer et améliorer vos projets.',
                'url' => route('services'),
            ],
        ]);
    }

    public function formation()
    {
        return view('pages.formation', [
            'seo' => [
                'title' => 'Formation',
                'description' => 'Catalogue de formations AgriFish: agriculture, aquaculture, gestion et business pour les professionnels du terrain.',
                'url' => route('formation'),
            ],
        ]);
    }

    public function contact()
    {
        return view('pages.contact', [
            'seo' => [
                'title' => 'Contact',
                'description' => 'Contactez AgriFish pour une demande de conseil, un projet, une étude technique ou une formation.',
                'url' => route('contact'),
            ],
        ]);
    }

    public function contactMerci()
    {
        return view('pages.contact-merci', [
            'seo' => [
                'title' => 'Message envoyé',
                'description' => 'Votre message a bien été envoyé à l’équipe AgriFish.',
                'url' => route('contact.merci'),
            ],
        ]);
    }

    public function mentionsLegales()
    {
        return view('pages.mentions-legales', [
            'seo' => [
                'title' => 'Mentions légales',
                'description' => 'Mentions légales du site AgriFish.',
                'url' => route('mentions-legales'),
            ],
        ]);
    }

    public function politiqueConfidentialite()
    {
        return view('pages.politique-confidentialite', [
            'seo' => [
                'title' => 'Confidentialité',
                'description' => 'Politique de confidentialité et traitement des données personnelles chez AgriFish.',
                'url' => route('politique-confidentialite'),
            ],
        ]);
    }

    public function caseStudies()
    {
        return view('pages.etudes-de-cas', [
            'seo' => [
                'title' => 'Réalisations et projets',
                'description' => 'Projets AgriFish, études de cas et galerie de réalisations sur le terrain.',
                'url' => route('case-studies'),
            ],
        ]);
    }
}

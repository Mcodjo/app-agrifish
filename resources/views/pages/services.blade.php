@extends('layouts.app')

@section('title', 'Services')
@section('meta_description', 'Conseil agricole, développement piscicole et études de faisabilité : les prestations d’AgriFish pour vos projets en Afrique de l’Ouest.')

@php $services = config('site.services'); @endphp

@section('content')
<x-page-header label="Services"
               title="Agriculture, aquaculture et études techniques"
               intro="Nous intervenons sur le terrain et à distance pour préparer, lancer et améliorer des projets de production, du diagnostic jusqu’au suivi." />

<nav class="sticky top-16 z-30 border-b border-line bg-white lg:top-[72px]" aria-label="Sur cette page">
    <div class="max-w-content flex gap-6 overflow-x-auto px-site text-[15px]">
        @foreach($services as $key => $service)
            <a href="#{{ $key }}" class="shrink-0 border-b-2 border-transparent py-4 text-muted hover:border-dark hover:text-dark">{{ $service['titre'] }}</a>
        @endforeach
        <a href="#methode" class="shrink-0 border-b-2 border-transparent py-4 text-muted hover:border-dark hover:text-dark">Méthode</a>
        <a href="#publics" class="shrink-0 border-b-2 border-transparent py-4 text-muted hover:border-dark hover:text-dark">Pour qui</a>
    </div>
</nav>

@foreach($services as $key => $service)
    <section id="{{ $key }}" class="max-w-content px-site py-16 lg:py-24 {{ $loop->first ? '' : 'border-t border-line' }}">
        <div class="grid items-start gap-10 lg:grid-cols-2 lg:gap-16">
            <div class="aspect-[4/3] overflow-hidden rounded-[20px] sm:rounded-panel {{ $loop->odd ? '' : 'lg:order-2' }}">
                <x-photo :name="$service['photo']" :alt="$service['photo_alt']" sizes="(min-width: 1024px) 560px, 100vw" />
            </div>
            <div class="lg:pt-4">
                <p class="text-sm text-muted">{{ $service['intitule'] }}</p>
                <h2 class="display-md mt-2">{{ $service['titre'] }}</h2>
                <p class="mt-4 text-[17px] leading-8 text-muted">{{ $service['resume'] }}</p>
                <dl class="mt-8 grid gap-x-8 gap-y-6 sm:grid-cols-2">
                    @foreach($service['prestations'] as [$prestation, $detail])
                        <div class="border-t border-line pt-4">
                            <dt class="font-semibold text-dark">{{ $prestation }}</dt>
                            <dd class="mt-1.5 text-[15px] leading-7 text-muted">{{ $detail }}</dd>
                        </div>
                    @endforeach
                </dl>
                <a href="{{ route('contact', ['sujet' => $service['intitule']]) }}" class="btn-secondary mt-9">Demander un devis</a>
            </div>
        </div>
    </section>
@endforeach

<section id="methode" class="max-w-content px-site pb-16 lg:pb-24">
    <div class="panel px-6 py-12 sm:px-10 lg:px-14 lg:py-16">
        <div class="grid gap-6 lg:grid-cols-2 lg:items-end">
            <h2 class="display-md max-w-md">Notre méthode d’intervention</h2>
            <p class="max-w-lg text-[17px] leading-8 text-muted">La même démarche pour chaque mission, quelle que soit sa taille. Un expert attitré suit votre projet du début à la fin.</p>
        </div>
        <x-steps :items="config('site.methode')" class="mt-12" />
    </div>
</section>

<section id="publics" class="border-t border-line">
    <div class="max-w-content px-site py-16 lg:py-24">
        <h2 class="display-md max-w-xl">Pour qui intervenons-nous ?</h2>
        <div class="mt-10 grid gap-x-8 gap-y-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach(config('site.publics') as [$public, $texte])
                <div class="border-t border-dark/15 pt-5">
                    <h3 class="font-semibold text-dark">{{ $public }}</h3>
                    <p class="mt-2 text-[15px] leading-7 text-muted">{{ $texte }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection

@section('cta')
    <x-cta-final title="Besoin d’un premier avis sur votre projet ?"
                 :secondary="['Déposer une demande', route('requests.create')]" />
@endsection

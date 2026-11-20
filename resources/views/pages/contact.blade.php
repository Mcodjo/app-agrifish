@extends('layouts.app')

@section('title', 'Contact')
@section('meta_description', 'Contactez AgriFish pour un conseil agricole, un projet piscicole, une étude de faisabilité ou une formation.')

@php
    $contact = config('site.contact');
    $sujets = ['Conseil agricole', 'Développement piscicole', 'Études de faisabilité', 'Formation', 'Suivi de projet', 'Demande de devis', 'Partenariat ou investissement', 'Autre'];
    $sujetChoisi = old('subject', in_array(request('sujet'), $sujets, true) ? request('sujet') : '');
    $formation = request('formation');
    $messageInitial = old('message', $formation ? 'Je souhaite m’inscrire à la formation « '.$formation.' ».' : '');
@endphp

@section('content')
<section class="max-w-content px-site pb-20 pt-14 sm:pt-20 lg:pb-28">
    <div class="grid gap-12 lg:grid-cols-[1fr_1.25fr] lg:gap-16">
        <div>
            <p class="text-sm font-medium text-muted">Contact</p>
            <h1 class="display-lg mt-5">Parlons de votre projet</h1>
            <p class="lead mt-6 max-w-md">Conseil, étude technique, projet agricole ou piscicole, formation : décrivez votre besoin, nous revenons vers vous sous {{ $contact['delai'] }}.</p>

            <dl class="mt-10 divide-y divide-line border-y border-line text-[15px]">
                <div class="flex items-baseline justify-between gap-6 py-4">
                    <dt class="text-muted">E-mail</dt>
                    <dd><a href="mailto:{{ $contact['email'] }}" class="font-medium text-dark underline-offset-4 hover:underline">{{ $contact['email'] }}</a></dd>
                </div>
                @if($contact['telephone'])
                    <div class="flex items-baseline justify-between gap-6 py-4">
                        <dt class="text-muted">Téléphone</dt>
                        <dd><a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact['telephone']) }}" class="font-medium text-dark">{{ $contact['telephone'] }}</a></dd>
                    </div>
                @endif
                @if($contact['whatsapp'])
                    <div class="flex items-baseline justify-between gap-6 py-4">
                        <dt class="text-muted">WhatsApp</dt>
                        <dd><a href="https://wa.me/{{ preg_replace('/\D/', '', $contact['whatsapp']) }}" target="_blank" rel="noopener noreferrer" class="font-medium text-dark underline-offset-4 hover:underline">Écrire sur WhatsApp</a></dd>
                    </div>
                @endif
                <div class="flex items-baseline justify-between gap-6 py-4">
                    <dt class="text-muted">Zone d’intervention</dt>
                    <dd class="font-medium text-dark">{{ $contact['zone'] }}</dd>
                </div>
            </dl>

            <div class="mt-10">
                <h2 class="font-semibold text-dark">Et ensuite ?</h2>
                <ol class="mt-4 space-y-3 text-[15px] leading-7 text-muted">
                    <li class="flex gap-3"><span class="font-serif text-gold-dark">01</span> Nous lisons votre message et revenons vers vous sous {{ $contact['delai'] }}.</li>
                    <li class="flex gap-3"><span class="font-serif text-gold-dark">02</span> Un premier échange permet de préciser le besoin.</li>
                    <li class="flex gap-3"><span class="font-serif text-gold-dark">03</span> Vous recevez une proposition d’intervention et un devis.</li>
                </ol>
            </div>
        </div>

        <div class="panel p-6 sm:p-10">
            <h2 class="text-xl font-semibold text-dark">Votre demande</h2>
            <p class="mt-1 text-sm text-muted">Les champs marqués d’un astérisque sont obligatoires.</p>

            @if($errors->any())
                <div class="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                    Certains champs sont à corriger.
                </div>
            @endif

            <form action="{{ route('contact.send') }}" method="POST" class="mt-8 space-y-5" x-data="{ envoi: false }" @submit="envoi = true">
                @csrf
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="field-label">Prénom *</label>
                        <input id="first_name" name="first_name" type="text" required maxlength="100" value="{{ old('first_name') }}" autocomplete="given-name" class="field-input @error('first_name') border-red-400 @enderror">
                        @error('first_name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="last_name" class="field-label">Nom *</label>
                        <input id="last_name" name="last_name" type="text" required maxlength="100" value="{{ old('last_name') }}" autocomplete="family-name" class="field-input @error('last_name') border-red-400 @enderror">
                        @error('last_name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="email" class="field-label">E-mail *</label>
                        <input id="email" name="email" type="email" required maxlength="255" value="{{ old('email') }}" autocomplete="email" class="field-input @error('email') border-red-400 @enderror">
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="phone" class="field-label">Téléphone</label>
                        <input id="phone" name="phone" type="tel" maxlength="30" value="{{ old('phone') }}" autocomplete="tel" class="field-input">
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="organisation" class="field-label">Organisation</label>
                        <input id="organisation" name="organisation" type="text" maxlength="150" value="{{ old('organisation') }}" autocomplete="organization" class="field-input">
                    </div>
                    <div>
                        <label for="subject" class="field-label">Sujet *</label>
                        <select id="subject" name="subject" required class="field-input @error('subject') border-red-400 @enderror">
                            <option value="">Choisir…</option>
                            @foreach($sujets as $sujet)
                                <option value="{{ $sujet }}" @selected($sujetChoisi === $sujet)>{{ $sujet }}</option>
                            @endforeach
                        </select>
                        @error('subject')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="message" class="field-label">Message *</label>
                    <textarea id="message" name="message" rows="6" required minlength="20" maxlength="3000"
                              class="field-input @error('message') border-red-400 @enderror"
                              placeholder="Lieu du projet, surface ou nombre de bassins, objectifs, calendrier…">{{ $messageInitial }}</textarea>
                    @error('message')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="flex items-start gap-3 text-sm leading-6 text-muted">
                        <input name="consent" type="checkbox" value="1" required class="mt-1 h-4 w-4 rounded border-line text-primary-800 focus:ring-primary-500" @checked(old('consent'))>
                        <span>J’accepte que mes données soient utilisées pour répondre à ma demande, conformément à la <a href="{{ route('politique-confidentialite') }}" class="font-medium text-dark underline underline-offset-2">politique de confidentialité</a>.</span>
                    </label>
                    @error('consent')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn-primary w-full sm:w-auto" :disabled="envoi">
                    <span x-text="envoi ? 'Envoi en cours…' : 'Envoyer ma demande'">Envoyer ma demande</span>
                </button>
            </form>
        </div>
    </div>
</section>
@endsection

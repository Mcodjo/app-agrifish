@extends('layouts.app')

@section('title', $seo['title'])
@section('meta_description', $seo['description'])
@section('og_title', $seo['title'])
@section('og_description', $seo['description'])
@section('og_url', $seo['url'])

@section('content')
<section class="relative overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 36px 36px;"></div>
    <div class="max-w-content mx-auto px-4 lg:px-8 pt-20 pb-20 lg:pt-28 lg:pb-24 relative z-10">
        <div class="max-w-3xl">
            <p class="text-sm font-bold uppercase tracking-[0.24em] text-gold-light">Demande de service</p>
            <h1 class="mt-4 text-4xl font-extrabold leading-tight sm:text-5xl lg:text-7xl">Déposez votre demande avec vos documents.</h1>
            <p class="mt-6 text-lg leading-8 text-white/78 lg:text-xl">Ajoutez des pièces jointes pour accélérer l’analyse: nous vous répondrons depuis votre espace client.</p>
        </div>
    </div>
</section>

<section class="bg-cream py-20 lg:py-24">
    <div class="max-w-content mx-auto px-4 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[0.95fr_1.05fr]">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100">
                <h2 class="text-2xl font-bold text-dark">Votre compte</h2>
                <p class="mt-3 text-sm leading-7 text-gray-600">Les demandes sont rattachées à votre profil. Vous pourrez ensuite suivre le statut dans le tableau de bord.</p>
                <div class="mt-6 space-y-3 text-sm text-gray-600">
                    <p><strong class="text-dark">Nom</strong><br>{{ $user->name }}</p>
                    <p><strong class="text-dark">Email</strong><br>{{ $user->email }}</p>
                    <p><strong class="text-dark">Téléphone</strong><br>{{ $user->phone ?? 'Non renseigné' }}</p>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-100 sm:p-8">
                <h2 class="text-2xl font-bold text-dark">Nouvelle demande</h2>

                <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data" class="mt-8 space-y-5">
                    @csrf
                    <div>
                        <label for="service_type" class="mb-2 block text-sm font-semibold text-gray-700">Type de service *</label>
                        <select id="service_type" name="service_type" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                            <option value="">-- Choisissez --</option>
                            <option value="agriculture" @selected(old('service_type') === 'agriculture')>Agriculture</option>
                            <option value="aquaculture" @selected(old('service_type') === 'aquaculture')>Aquaculture</option>
                            <option value="etude-technique" @selected(old('service_type') === 'etude-technique')>Étude technique</option>
                            <option value="formation" @selected(old('service_type') === 'formation')>Formation</option>
                            <option value="autre" @selected(old('service_type') === 'autre')>Autre</option>
                        </select>
                    </div>

                    <div>
                        <label for="title" class="mb-2 block text-sm font-semibold text-gray-700">Titre de la demande *</label>
                        <input id="title" name="title" type="text" value="{{ old('title') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ex: Diagnostic de parcelle de 5 hectares">
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">Description *</label>
                        <textarea id="description" name="description" rows="7" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Expliquez le contexte, les objectifs et le besoin...">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="priority" class="mb-2 block text-sm font-semibold text-gray-700">Priorité *</label>
                            <select id="priority" name="priority" class="w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                                <option value="normal" @selected(old('priority', 'normal') === 'normal')>Normale</option>
                                <option value="low" @selected(old('priority') === 'low')>Faible</option>
                                <option value="high" @selected(old('priority') === 'high')>Haute</option>
                            </select>
                        </div>

                        <div>
                            <label for="documents" class="mb-2 block text-sm font-semibold text-gray-700">Documents</label>
                            <input id="documents" name="documents[]" type="file" multiple class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-xl file:border-0 file:bg-primary file:px-4 file:py-3 file:text-sm file:font-semibold file:text-white hover:file:bg-primary-dark">
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                            <p class="font-semibold">Veuillez corriger les erreurs ci-dessous.</p>
                            <ul class="mt-2 space-y-1 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary px-6 py-4 font-bold text-white transition hover:bg-primary-dark">Envoyer la demande</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

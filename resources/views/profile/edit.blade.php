@extends('layouts.app')

@section('title', 'Profil')
@section('meta_description', 'Gérez votre profil AgriFish, votre mot de passe et votre compte.')

@section('content')
<section class="relative pt-32 pb-20 overflow-hidden bg-primary-dark text-white">
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    <div class="max-w-7xl mx-auto px-4 lg:px-8 relative z-10">
        <div class="max-w-3xl">
            <span class="inline-block py-1.5 px-4 bg-gold/20 text-gold text-xs font-bold uppercase tracking-widest rounded-full mb-6 border border-gold/30 backdrop-blur-sm">Mon compte</span>
            <h1 class="text-4xl lg:text-6xl font-extrabold mb-6 leading-tight">Paramètres de profil</h1>
            <p class="text-lg text-white/75 leading-relaxed">Mettez à jour vos informations personnelles, votre mot de passe et vos paramètres de compte.</p>
        </div>
    </div>
</section>

<section class="py-20 bg-cream">
    <div class="max-w-7xl mx-auto px-4 lg:px-8 space-y-6">
        <div class="p-6 sm:p-8 bg-white shadow-sm rounded-3xl border border-gray-100">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white shadow-sm rounded-3xl border border-gray-100">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-6 sm:p-8 bg-white shadow-sm rounded-3xl border border-gray-100">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</section>
@endsection

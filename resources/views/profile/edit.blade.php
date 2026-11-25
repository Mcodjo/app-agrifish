@extends('layouts.espace')

@section('title', 'Mon profil')

@section('content')
<x-espace.header :back="route('dashboard')" back-label="Vue d’ensemble" eyebrow="Mon compte"
              title="Profil et paramètres"
              subtitle="Vos coordonnées, vos préférences de notification et la sécurité de votre compte." />

<section class="mx-auto max-w-6xl px-5 py-8 sm:px-8 lg:py-10">
    <div class="max-w-3xl space-y-4">
        <div class="rounded-xl border border-line bg-white p-5 sm:p-8">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="rounded-xl border border-line bg-white p-5 sm:p-8">
            @include('profile.partials.update-password-form')
        </div>

        <div class="rounded-xl border border-line bg-white p-5 sm:p-8">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</section>
@endsection

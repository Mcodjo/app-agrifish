@extends('layouts.app')

@section('title', 'Mon profil')
@section('espace', true)

@section('content')
<x-app-header :back="route('dashboard')" back-label="Tableau de bord" eyebrow="Mon compte"
              title="Profil et paramètres"
              subtitle="Vos coordonnées, vos préférences de notification et la sécurité de votre compte." />

<section class="max-w-content px-site py-10 lg:py-12">
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

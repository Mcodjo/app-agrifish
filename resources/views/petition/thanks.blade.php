@extends('layouts.app')

@section('title', 'Merci · '.config('petition.title'))

@section('content')
<div class="center">
    @if (session('confirmed'))
        <p class="eyebrow">Signature confirmée</p>
        <h1>Merci, votre voix compte.</h1>
        <p>Nous sommes désormais {{ number_format($count, 0, ',', ' ') }} à demander que les événements tech viennent aussi à Porto-Novo. Partagez la pétition pour aller plus loin.</p>
    @elseif (session('pending'))
        <p class="eyebrow">Encore une étape</p>
        <h1>Confirmez votre signature dans votre boîte mail.</h1>
        <p>Nous venons de vous envoyer un e-mail. Cliquez sur le lien qu’il contient pour que votre signature soit comptée. Pensez à regarder dans les courriers indésirables.</p>
    @else
        <h1>Merci pour votre soutien.</h1>
        <p>Partagez la pétition autour de vous.</p>
    @endif

    @include('petition._share')
    <p style="margin-top: 20px"><a href="{{ route('petition.index') }}">Retour à la pétition</a></p>
</div>
@endsection

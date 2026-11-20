@props(['title', 'intro' => null, 'updated', 'sections'])

<section class="border-b border-line">
    <div class="max-w-content px-site pb-12 pt-14 sm:pt-20">
        <h1 class="display-lg">{{ $title }}</h1>
        @if($intro)
            <p class="lead mt-5 max-w-2xl">{{ $intro }}</p>
        @endif
        <p class="mt-5 text-sm text-muted">Dernière mise à jour : {{ $updated }}</p>
    </div>
</section>

<section class="max-w-content px-site py-14 lg:py-20">
    <div class="grid gap-12 lg:grid-cols-[15rem_1fr] lg:gap-16">
        <nav class="hidden lg:block" aria-label="Sommaire">
            <ol class="sticky top-28 space-y-2.5 text-sm">
                @foreach($sections as $id => $titre)
                    <li><a href="#{{ $id }}" class="text-muted hover:text-dark">{{ $titre }}</a></li>
                @endforeach
            </ol>
        </nav>
        <div class="prose-legal max-w-3xl">
            {{ $slot }}
        </div>
    </div>
</section>

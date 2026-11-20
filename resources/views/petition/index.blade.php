@extends('layouts.app')

@section('content')
<header class="hero">
    <p class="eyebrow">Pétition citoyenne · Porto-Novo, Bénin</p>
    <h1>{{ config('petition.title') }}</h1>
    <p class="lede">Un plaidoyer pour la décentralisation des événements tech et de l’innovation : hackathons, conférences, ateliers et programmes d’incubation dans la ville capitale.</p>
</header>

<div class="grid">
    <article class="letter">
        <h2>Le constat</h2>
        <p>Je suis Maurice Michel Hotegni CODJO, technicien en systèmes informatiques et logiciels, citoyen de Porto-Novo et passionné par le numérique, l’innovation et les opportunités qu’ils offrent à la jeunesse.</p>
        <p>À la suite de ma récente publication sur le manque d’événements technologiques d’envergure dans notre ville, j’ai été particulièrement marqué par les nombreuses réactions et les témoignages de jeunes qui partagent ce constat.</p>
        <p>Porto-Novo regorge de jeunes passionnés de technologie, de développeurs, de créatifs, d’étudiants et de porteurs de projets qui ont soif d’apprendre, de se former, de rencontrer d’autres acteurs du secteur, de partager leurs expériences et de saisir de nouvelles opportunités.</p>
        <p>Pourtant, une grande partie des grands rendez-vous du numérique, des conférences, des hackathons, des ateliers et des rencontres autour de l’innovation se concentre principalement à Cotonou. Pour y participer, de nombreux jeunes de Porto-Novo doivent faire face à des contraintes de déplacement, de transport et parfois de moyens financiers.</p>

        <blockquote>Non pas pour opposer Porto-Novo à Cotonou, mais pour faire avancer une vision plus inclusive et décentralisée de l’innovation.</blockquote>

        <h2>Notre demande</h2>
        <p>Mon plaidoyer n’est pas de demander que tous les événements technologiques soient déplacés à Porto-Novo. Cotonou a naturellement sa place dans cette dynamique et doit continuer à accueillir de grandes initiatives.</p>
        <p>Je demande simplement que Porto-Novo puisse, elle aussi, accueillir une partie de ces événements. Que les organisateurs, les entreprises du numérique, les communautés tech, les institutions et les autorités pensent également à la ville capitale dans leurs calendriers et leurs choix de lieux.</p>
        <p>Un hackathon à Porto-Novo, une conférence sur l’intelligence artificielle, un atelier de formation, une rencontre entre développeurs ou un programme d’incubation pourraient permettre à de nombreux jeunes de découvrir de nouvelles perspectives, de développer leurs compétences et de concrétiser leurs idées.</p>

        <h2>Une vision de développement équilibré</h2>
        <p>Nous saluons la vision du gouvernement en matière de développement territorial, notamment à travers la dynamique des pôles de développement visant à valoriser le potentiel de nos différentes régions. Nous espérons que cette ambition de développement équilibré se traduira également dans l’écosystème numérique par une meilleure répartition des événements, des initiatives et des opportunités.</p>
        <p>Le développement de l’écosystème numérique béninois ne devrait pas se limiter à une seule ville. Il doit rayonner, créer des opportunités dans nos différents territoires et permettre à chaque jeune de participer à cette dynamique, peu importe où il vit.</p>
        <p><strong>Porto-Novo a du potentiel. Porto-Novo a des talents. Porto-Novo a une jeunesse qui mérite aussi d’être exposée aux grandes opportunités de l’écosystème numérique.</strong></p>

        <h2>À qui s’adresse cette pétition</h2>
        <p>Au Gouvernement du Bénin, à la Présidence de la République, au Ministère du Numérique et de la Digitalisation, à Monsieur le Ministre Wilfried Léandre Houngbedji, à Dada Stéphanie M. Montcho, ainsi qu’aux organisateurs d’événements, aux structures d’innovation et aux acteurs du numérique au Bénin.</p>
        <p>J’invite toute la communauté tech, les jeunes, les entrepreneurs, les étudiants et les citoyens qui partagent ce constat à signer et à partager massivement cette pétition. Que les événements tech viennent aussi à Porto-Novo. Notre ville a sa place dans cette dynamique.</p>
        <p class="sig">Un fils du pays, inquiet pour la jeunesse de Porto-Novo.</p>
        <div class="tags">
            @foreach (config('petition.hashtags') as $tag)<span>#{{ $tag }}</span>@endforeach
        </div>
    </article>

    <aside class="side">
        <section class="card">
            <div class="count">{{ number_format($count, 0, ',', ' ') }}</div>
            <p class="count-label">{{ $count > 1 ? 'signatures' : 'signature' }}</p>
            @php $pct = min(100, (int) round($count / max(1, $goal) * 100)); @endphp
            <div class="bar" role="progressbar" aria-label="Progression vers l’objectif" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $pct }}"><span style="width: {{ $pct }}%"></span></div>
            <div class="goal"><span>{{ $pct }} % de l’objectif</span><span>{{ number_format($goal, 0, ',', ' ') }} signatures</span></div>
        </section>

        <section class="card" id="signer">
            <form class="sign" method="POST" action="{{ route('petition.store') }}" novalidate>
                @csrf
                <h2>Je signe la pétition</h2>

                @if (session('status'))
                    <div class="alert ok">{{ session('status') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert bad" role="alert">Vérifiez les champs signalés ci-dessous.</div>
                @endif

                <div class="two">
                    <label for="first_name">Prénom
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" maxlength="60" autocomplete="given-name" required @class(['invalid' => $errors->has('first_name')])>
                        @error('first_name')<span class="err">{{ $message }}</span>@enderror
                    </label>
                    <label for="last_name">Nom
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" maxlength="60" autocomplete="family-name" required @class(['invalid' => $errors->has('last_name')])>
                        @error('last_name')<span class="err">{{ $message }}</span>@enderror
                    </label>
                </div>

                <label for="email">Adresse e-mail <small>(jamais publiée)</small>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" maxlength="255" autocomplete="email" required @class(['invalid' => $errors->has('email')])>
                    @error('email')<span class="err">{{ $message }}</span>@enderror
                </label>

                <label for="city">Commune ou ville
                    <input type="text" id="city" name="city" value="{{ old('city') }}" maxlength="60" placeholder="Porto-Novo, Adjarra, Cotonou…" required @class(['invalid' => $errors->has('city')])>
                    @error('city')<span class="err">{{ $message }}</span>@enderror
                </label>

                <label for="profile">Je suis
                    <select id="profile" name="profile" @class(['invalid' => $errors->has('profile')])>
                        @foreach (config('petition.profiles') as $key => $label)
                            <option value="{{ $key }}" @selected(old('profile', 'citoyen') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('profile')<span class="err">{{ $message }}</span>@enderror
                </label>

                <label for="comment">Un mot pour les décideurs <small>(facultatif, 280 caractères)</small>
                    <textarea id="comment" name="comment" maxlength="280">{{ old('comment') }}</textarea>
                    @error('comment')<span class="err">{{ $message }}</span>@enderror
                </label>

                <div class="hp" aria-hidden="true">
                    <label for="website">Ne pas remplir <input type="text" id="website" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <label class="check" for="show_name"><input type="checkbox" id="show_name" name="show_name" value="1" @checked(old('show_name', true))> <span>Afficher mon prénom et l’initiale de mon nom dans la liste publique.</span></label>
                <label class="check" for="accepts_updates"><input type="checkbox" id="accepts_updates" name="accepts_updates" value="1" @checked(old('accepts_updates'))> <span>Recevoir des nouvelles de la pétition et des événements tech à Porto-Novo.</span></label>
                <label class="check" for="consent"><input type="checkbox" id="consent" name="consent" value="1" @checked(old('consent'))> <span>J’accepte que mes données soient utilisées pour cette pétition et remises aux destinataires.</span></label>
                @error('consent')<span class="err">{{ $message }}</span>@enderror

                <button class="btn" type="submit">Signer maintenant</button>
                @if (config('petition.require_verification'))
                    <p class="err" style="color: var(--muted); font-weight: 400; margin: 0">Vous recevrez un e-mail pour confirmer votre signature.</p>
                @endif
            </form>
        </section>

        @if ($cities->isNotEmpty())
            <section class="card mini">
                <h3>Communes les plus mobilisées</h3>
                <ol>
                    @foreach ($cities as $c)
                        <li>{{ $c->city }} <span>{{ $c->total }}</span></li>
                    @endforeach
                </ol>
            </section>
        @endif
    </aside>
</div>

<section class="wall" aria-labelledby="wallTitle">
    <div class="wall-head">
        <h2 id="wallTitle">Ils et elles ont signé</h2>
        @if ($count > 0)<a href="{{ route('petition.signatures') }}">Voir les {{ $count }} signataires</a>@endif
    </div>
    <div class="list">
        @forelse ($recent as $s)
            @include('petition._signature', ['s' => $s])
        @empty
            <div class="empty">Aucune signature pour l’instant. Soyez la première personne à signer et à partager cette pétition.</div>
        @endforelse
    </div>
</section>

<section class="wall">
    <div class="wall-head"><h2>Faites entendre cette voix</h2></div>
    @include('petition._share')
</section>
@endsection

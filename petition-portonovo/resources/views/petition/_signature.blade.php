<div class="sg">
    <b>{{ $s->publicName() }}</b>
    <span class="meta">{{ $s->city }} · {{ $s->profileLabel() }} · {{ $s->created_at->translatedFormat('j M Y') }}</span>
    @if ($s->comment)
        <q>{{ $s->comment }}</q>
    @endif
</div>

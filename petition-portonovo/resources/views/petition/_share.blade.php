@php
    $shareUrl = url('/');
    $shareText = 'Je soutiens la pétition « '.config('petition.title').' ». Porto-Novo a des talents : signez et partagez !';
    $tags = collect(config('petition.hashtags'))->map(fn ($t) => '#'.$t)->implode(' ');
    $full = $shareText.' '.$shareUrl.' '.$tags;
@endphp
<div class="row">
    <a class="btn" href="https://wa.me/?text={{ rawurlencode($full) }}" target="_blank" rel="noopener">Partager sur WhatsApp</a>
    <a class="btn ghost" href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener">Facebook</a>
    <a class="btn ghost" href="https://www.linkedin.com/sharing/share-offsite/?url={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener">LinkedIn</a>
    <a class="btn ghost" href="https://x.com/intent/post?text={{ rawurlencode($shareText.' '.$tags) }}&url={{ rawurlencode($shareUrl) }}" target="_blank" rel="noopener">X</a>
    <button class="btn ghost" type="button" data-copy="{{ $full }}">Copier le message</button>
</div>
<p class="copy-ok" aria-live="polite"></p>
@once
@push('scripts')
<script>
document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-copy]');
    if (!b) return;
    var text = b.getAttribute('data-copy');
    var note = b.parentElement.nextElementSibling;
    var ok = function () { if (note) note.textContent = 'Message copié. Collez-le dans votre publication.'; };
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(ok, function () { if (note) note.textContent = text; });
    } else if (note) { note.textContent = text; }
});
</script>
@endpush
@endonce

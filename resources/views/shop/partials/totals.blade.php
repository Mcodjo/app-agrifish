<dl class="mt-4 space-y-2 text-sm">
    <div class="flex justify-between"><dt class="text-muted">Sous-total ({{ $summary['count'] }} {{ $summary['count'] > 1 ? 'articles' : 'article' }})</dt><dd class="text-dark">{{ money($summary['subtotal']) }}</dd></div>
    @if($summary['discount'] > 0)
        <div class="flex justify-between text-primary-800"><dt>Réduction ({{ $summary['coupon']->code }})</dt><dd>-{{ money($summary['discount']) }}</dd></div>
    @endif
    <div class="flex justify-between border-t border-line pt-3 text-base"><dt class="font-semibold text-dark">Total TTC</dt><dd class="font-semibold text-dark">{{ money($summary['total']) }}</dd></div>
</dl>

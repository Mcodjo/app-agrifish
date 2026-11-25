@if($item->product_id && $item->product && ! $item->product->trashed())
    @if(in_array($item->product_id, $reviewed, true))
        <p class="mt-2 text-xs text-muted">Merci, vous avez noté ce produit.</p>
    @else
        <div x-data="{ open: false, rating: 0 }" class="mt-2">
            <button type="button" x-show="!open" @click="open = true" class="text-sm font-medium text-primary-800 hover:underline">Noter ce produit</button>
            <form x-show="open" x-cloak method="POST" action="{{ route('account.reviews.store') }}" class="mt-2 space-y-3 rounded-lg bg-cream p-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                <input type="hidden" name="rating" :value="rating">
                <div class="flex items-center gap-1" role="radiogroup" aria-label="Note sur 5">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" @click="rating = {{ $i }}" role="radio" :aria-checked="rating === {{ $i }}" aria-label="{{ $i }} sur 5"
                                :class="rating >= {{ $i }} ? 'text-gold' : 'text-line'" class="p-0.5">
                            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path d="M10 1.5l2.6 5.3 5.9.9-4.3 4.1 1 5.8L10 14.9l-5.2 2.7 1-5.8L1.5 7.7l5.9-.9L10 1.5z"/></svg>
                        </button>
                    @endfor
                </div>
                <label class="sr-only" for="comment-{{ $item->id }}">Votre avis</label>
                <textarea id="comment-{{ $item->id }}" name="comment" rows="2" maxlength="1500" class="field-input text-sm" placeholder="Votre avis sur ce produit (facultatif)"></textarea>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary !py-1.5 text-sm" :disabled="rating === 0">Envoyer</button>
                    <button type="button" @click="open = false" class="btn-outline !py-1.5 text-sm">Annuler</button>
                </div>
            </form>
        </div>
    @endif
@endif

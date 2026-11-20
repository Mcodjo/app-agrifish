@props(['items'])

<div {{ $attributes->merge(['class' => 'divide-y divide-line border-y border-line']) }} x-data="{ open: 0 }">
    @foreach($items as $i => [$question, $reponse])
        <div>
            <h3>
                <button type="button" class="flex w-full items-center justify-between gap-6 py-5 text-left text-[17px] font-semibold text-dark"
                        @click="open = open === {{ $i }} ? null : {{ $i }}" :aria-expanded="open === {{ $i }}">
                    {{ $question }}
                    <svg class="h-5 w-5 shrink-0 text-muted transition-transform duration-200" :class="open === {{ $i }} ? 'rotate-45' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                </button>
            </h3>
            <div x-show="open === {{ $i }}" x-collapse @if($i !== 0) x-cloak @endif>
                <p class="max-w-2xl pb-6 leading-7 text-muted">{{ $reponse }}</p>
            </div>
        </div>
    @endforeach
</div>

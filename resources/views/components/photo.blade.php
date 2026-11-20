@props(['name', 'alt' => '', 'sizes' => '(min-width: 1024px) 50vw, 100vw', 'eager' => false])

<img src="{{ asset('images/photos/'.$name.'.webp') }}"
     srcset="{{ asset('images/photos/'.$name.'-640.webp') }} 640w, {{ asset('images/photos/'.$name.'.webp') }} 1024w"
     sizes="{{ $sizes }}"
     width="1024" height="683"
     alt="{{ $alt }}"
     @if($eager) fetchpriority="high" @else loading="lazy" decoding="async" @endif
     {{ $attributes->merge(['class' => 'h-full w-full object-cover']) }}>

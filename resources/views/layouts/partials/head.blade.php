<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="@yield('og_url', url()->current())">
<meta property="og:site_name" content="AgriFish">
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:locale" content="fr_FR">
<meta property="og:title" content="@yield('og_title', $fullTitle)">
<meta property="og:description" content="@yield('og_description', $description)">
<meta property="og:url" content="@yield('og_url', url()->current())">
<meta property="og:image" content="@yield('og_image', asset('images/og-agrifish.jpg'))">
<meta name="twitter:card" content="summary_large_image">
<meta name="theme-color" content="#1b4332">
@if($noindex ?? false)
    <meta name="robots" content="noindex">
@endif
@stack('head')

@vite(['resources/css/app.css', 'resources/js/app.js'])

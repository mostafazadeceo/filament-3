<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $seo = $seo ?? [];
        $title = $seo['title'] ?? ($page->title ?? ($site->name ?? '')); 
        $description = $seo['description'] ?? null;
        $image = $seo['image'] ?? null;
        $ogType = $seo['og_type'] ?? 'website';
        $themeTokens = $theme?->tokens ?? [];
        $themeAssets = $theme?->assets ?? [];
    @endphp
    <title>{{ $title }}</title>
    @if($description)
        <meta name="description" content="{{ $description }}">
    @endif
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:type" content="{{ $ogType }}">
    @if($description)
        <meta property="og:description" content="{{ $description }}">
    @endif
    @if($image)
        <meta property="og:image" content="{{ $image }}">
    @endif
    @if(isset($themeAssets['css']))
        <link rel="stylesheet" href="{{ asset($themeAssets['css']) }}">
    @endif
    <style>
        :root {
            @if(isset($themeTokens['primary'])) --theme-primary: {{ $themeTokens['primary'] }}; @endif
            @if(isset($themeTokens['background'])) --theme-bg: {{ $themeTokens['background'] }}; @endif
            @if(isset($themeTokens['text'])) --theme-text: {{ $themeTokens['text'] }}; @endif
        }
        body { margin: 0; font-family: "Vazirmatn", sans-serif; background: var(--theme-bg, #f9f9fb); color: var(--theme-text, #0f172a); }
        header { padding: 24px; border-bottom: 1px solid rgba(15, 23, 42, 0.08); }
        main { max-width: 960px; margin: 0 auto; padding: 32px 24px; }
        a { color: var(--theme-primary, #E84140); }
        .page-title { font-size: 32px; margin-bottom: 16px; }
        .page-body { line-height: 1.9; }
    </style>
</head>
<body>
    <header>
        <div>{{ $site->name ?? 'سایت' }}</div>
    </header>
    <main>
        @yield('content')
    </main>
</body>
</html>

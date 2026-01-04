@extends('content-cms::layouts.site')

@section('content')
    <h1 class="page-title">{{ $page->title }}</h1>

    <div class="page-body">
        @php
            $payload = $page->published_content ?? $page->draft_content ?? [];
            $rendered = false;
        @endphp

        @if(is_array($payload) && isset($payload['sections']) && is_array($payload['sections']))
            @foreach($payload['sections'] as $section)
                @php
                    $settings = is_array($section['settings'] ?? null) ? $section['settings'] : [];
                @endphp
                <section style="margin-bottom: 24px;">
                    @if(!empty($settings['title']))
                        <h2>{{ $settings['title'] }}</h2>
                    @endif
                    @if(!empty($settings['subtitle']))
                        <p>{{ $settings['subtitle'] }}</p>
                    @endif
                    @if(!empty($settings['html']))
                        {!! $settings['html'] !!}
                        @php $rendered = true; @endphp
                    @elseif(!empty($settings['content']))
                        {!! $settings['content'] !!}
                        @php $rendered = true; @endphp
                    @endif

                    @if(isset($section['blocks']) && is_array($section['blocks']))
                        @foreach($section['blocks'] as $block)
                            @php
                                $blockSettings = is_array($block['settings'] ?? null) ? $block['settings'] : [];
                            @endphp
                            <div style="margin-bottom: 12px;">
                                @if(!empty($blockSettings['title']))
                                    <h3>{{ $blockSettings['title'] }}</h3>
                                @endif
                                @if(!empty($blockSettings['html']))
                                    {!! $blockSettings['html'] !!}
                                    @php $rendered = true; @endphp
                                @elseif(!empty($blockSettings['content']))
                                    {!! $blockSettings['content'] !!}
                                    @php $rendered = true; @endphp
                                @endif
                            </div>
                        @endforeach
                    @endif
                </section>
            @endforeach
        @endif

        @if(! $rendered)
            <pre>{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        @endif
    </div>
@endsection

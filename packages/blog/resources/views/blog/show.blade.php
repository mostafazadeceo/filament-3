@extends('blog::layouts.site')

@section('content')
    <h1 class="post-title">{{ $post->title }}</h1>

    @if($post->excerpt)
        <p>{{ $post->excerpt }}</p>
    @endif

    <div class="post-body">
        {!! $post->published_content ?? $post->draft_content !!}
    </div>
@endsection

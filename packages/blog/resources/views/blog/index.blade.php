@extends('blog::layouts.site')

@section('content')
    <h1 style="margin-bottom: 24px;">وبلاگ</h1>

    @forelse($posts as $post)
        <article style="margin-bottom: 32px;">
            <h2 style="margin-bottom: 8px;">
                <a href="/{{ config('blog.public.prefix', 'blog') }}/{{ $post->slug }}">
                    {{ $post->title }}
                </a>
            </h2>
            @if($post->excerpt)
                <p>{{ $post->excerpt }}</p>
            @endif
        </article>
    @empty
        <p>هنوز نوشته ای منتشر نشده است.</p>
    @endforelse

    <div>
        {{ $posts->links() }}
    </div>
@endsection

<?php

namespace Haida\Blog\Services;

use Haida\Blog\Models\BlogPost;
use Haida\PageBuilder\Services\HtmlSanitizer;
use Illuminate\Support\Facades\DB;

class BlogPostService
{
    public function __construct(private HtmlSanitizer $sanitizer) {}

    public function publish(BlogPost $post, ?int $actorUserId = null): BlogPost
    {
        return DB::transaction(function () use ($post, $actorUserId): BlogPost {
            $draft = $post->draft_content ?? '';
            $published = $this->sanitizer->sanitize((string) $draft);

            $post->published_content = $published;
            $post->status = 'published';
            $post->published_at = now();
            $post->updated_by_user_id = $actorUserId;
            $post->save();

            return $post;
        });
    }

    public function rollbackToPublished(BlogPost $post, ?int $actorUserId = null): BlogPost
    {
        return DB::transaction(function () use ($post, $actorUserId): BlogPost {
            $post->draft_content = $post->published_content;
            $post->status = 'draft';
            $post->updated_by_user_id = $actorUserId;
            $post->save();

            return $post;
        });
    }
}

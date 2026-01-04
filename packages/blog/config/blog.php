<?php

return [
    'tables' => [
        'posts' => 'blog_posts',
        'categories' => 'blog_categories',
        'tags' => 'blog_tags',
        'post_tag' => 'blog_post_tag',
    ],
    'public' => [
        'prefix' => 'blog',
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
];

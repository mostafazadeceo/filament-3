<?php

return [
    'tables' => [
        'templates' => 'page_builder_templates',
        'revisions' => 'page_builder_revisions',
    ],
    'sanitize' => [
        'allowed_tags' => [
            'a', 'p', 'strong', 'em', 'ul', 'ol', 'li', 'br', 'h2', 'h3', 'h4', 'blockquote', 'code', 'pre', 'span',
        ],
        'allowed_attributes' => [
            'a' => ['href', 'title', 'target', 'rel'],
            'span' => ['class'],
            'p' => ['class'],
            'code' => ['class'],
        ],
        'sanitize_keys' => ['html', 'rich_text', 'content', 'body', 'description'],
    ],
];

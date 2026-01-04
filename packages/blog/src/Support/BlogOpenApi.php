<?php

namespace Haida\Blog\Support;

class BlogOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Blog API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/blog/posts' => [
                    'get' => ['summary' => 'List posts'],
                    'post' => ['summary' => 'Create post'],
                ],
                '/api/v1/blog/posts/{post}' => [
                    'get' => ['summary' => 'Show post'],
                    'put' => ['summary' => 'Update post'],
                    'delete' => ['summary' => 'Delete post'],
                ],
                '/api/v1/blog/categories' => [
                    'get' => ['summary' => 'List categories'],
                    'post' => ['summary' => 'Create category'],
                ],
                '/api/v1/blog/categories/{category}' => [
                    'get' => ['summary' => 'Show category'],
                    'put' => ['summary' => 'Update category'],
                    'delete' => ['summary' => 'Delete category'],
                ],
                '/api/v1/blog/tags' => [
                    'get' => ['summary' => 'List tags'],
                    'post' => ['summary' => 'Create tag'],
                ],
                '/api/v1/blog/tags/{tag}' => [
                    'get' => ['summary' => 'Show tag'],
                    'put' => ['summary' => 'Update tag'],
                    'delete' => ['summary' => 'Delete tag'],
                ],
                '/api/v1/blog/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

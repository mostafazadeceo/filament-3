<?php

namespace Haida\ContentCms\Support;

class ContentCmsOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Content CMS API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/content-cms/pages' => [
                    'get' => ['summary' => 'List pages'],
                    'post' => ['summary' => 'Create page'],
                ],
                '/api/v1/content-cms/pages/{page}' => [
                    'get' => ['summary' => 'Show page'],
                    'put' => ['summary' => 'Update page'],
                    'delete' => ['summary' => 'Delete page'],
                ],
                '/api/v1/content-cms/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

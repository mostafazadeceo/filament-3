<?php

namespace Haida\FilamentPettyCashIr\Support;

class PettyCashOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Petty Cash API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/petty-cash/funds' => [
                    'get' => ['summary' => 'List funds'],
                    'post' => ['summary' => 'Create fund'],
                ],
                '/api/v1/petty-cash/funds/{fund}' => [
                    'get' => ['summary' => 'Show fund'],
                    'put' => ['summary' => 'Update fund'],
                    'delete' => ['summary' => 'Delete fund'],
                ],
                '/api/v1/petty-cash/categories' => [
                    'get' => ['summary' => 'List categories'],
                    'post' => ['summary' => 'Create category'],
                ],
                '/api/v1/petty-cash/categories/{category}' => [
                    'get' => ['summary' => 'Show category'],
                    'put' => ['summary' => 'Update category'],
                    'delete' => ['summary' => 'Delete category'],
                ],
                '/api/v1/petty-cash/expenses' => [
                    'get' => ['summary' => 'List expenses'],
                    'post' => ['summary' => 'Create expense'],
                ],
                '/api/v1/petty-cash/expenses/{expense}' => [
                    'get' => ['summary' => 'Show expense'],
                    'put' => ['summary' => 'Update expense'],
                    'delete' => ['summary' => 'Delete expense'],
                ],
                '/api/v1/petty-cash/expenses/{expense}/submit' => [
                    'post' => ['summary' => 'Submit expense'],
                ],
                '/api/v1/petty-cash/expenses/{expense}/approve' => [
                    'post' => ['summary' => 'Approve expense'],
                ],
                '/api/v1/petty-cash/expenses/{expense}/reject' => [
                    'post' => ['summary' => 'Reject expense'],
                ],
                '/api/v1/petty-cash/expenses/{expense}/post' => [
                    'post' => ['summary' => 'Post expense'],
                ],
                '/api/v1/petty-cash/expenses/{expense}/ai-suggest' => [
                    'post' => ['summary' => 'AI suggest expense fields'],
                ],
                '/api/v1/petty-cash/expenses/{expense}/ai-apply' => [
                    'post' => ['summary' => 'Apply AI suggestion to expense'],
                ],
                '/api/v1/petty-cash/expenses/{expense}/ai-reject' => [
                    'post' => ['summary' => 'Reject AI suggestion for expense'],
                ],
                '/api/v1/petty-cash/replenishments' => [
                    'get' => ['summary' => 'List replenishments'],
                    'post' => ['summary' => 'Create replenishment'],
                ],
                '/api/v1/petty-cash/replenishments/{replenishment}' => [
                    'get' => ['summary' => 'Show replenishment'],
                    'put' => ['summary' => 'Update replenishment'],
                    'delete' => ['summary' => 'Delete replenishment'],
                ],
                '/api/v1/petty-cash/replenishments/{replenishment}/submit' => [
                    'post' => ['summary' => 'Submit replenishment'],
                ],
                '/api/v1/petty-cash/replenishments/{replenishment}/approve' => [
                    'post' => ['summary' => 'Approve replenishment'],
                ],
                '/api/v1/petty-cash/replenishments/{replenishment}/reject' => [
                    'post' => ['summary' => 'Reject replenishment'],
                ],
                '/api/v1/petty-cash/replenishments/{replenishment}/post' => [
                    'post' => ['summary' => 'Post replenishment'],
                ],
                '/api/v1/petty-cash/settlements' => [
                    'get' => ['summary' => 'List settlements'],
                    'post' => ['summary' => 'Create settlement'],
                ],
                '/api/v1/petty-cash/settlements/{settlement}' => [
                    'get' => ['summary' => 'Show settlement'],
                    'put' => ['summary' => 'Update settlement'],
                    'delete' => ['summary' => 'Delete settlement'],
                ],
                '/api/v1/petty-cash/settlements/{settlement}/submit' => [
                    'post' => ['summary' => 'Submit settlement'],
                ],
                '/api/v1/petty-cash/settlements/{settlement}/approve' => [
                    'post' => ['summary' => 'Approve settlement'],
                ],
                '/api/v1/petty-cash/settlements/{settlement}/post' => [
                    'post' => ['summary' => 'Post settlement'],
                ],
                '/api/v1/petty-cash/ai/audit' => [
                    'post' => ['summary' => 'Run AI continuous audit'],
                ],
                '/api/v1/petty-cash/ai/report' => [
                    'get' => ['summary' => 'Get AI management report'],
                ],
                '/api/v1/petty-cash/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

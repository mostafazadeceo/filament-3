<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Support;

final class AppOpenApi
{
    /**
     * @return array<string, mixed>
     */
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Haida App API',
                'version' => '1.0.0',
                'description' => 'App-facing endpoints for mobile/web clients.',
            ],
            'paths' => [
                '/api/v1/app/auth/login' => [
                    'post' => ['summary' => 'Login and issue tokens'],
                ],
                '/api/v1/app/auth/refresh' => [
                    'post' => ['summary' => 'Refresh access token'],
                ],
                '/api/v1/app/auth/logout' => [
                    'post' => ['summary' => 'Logout current session'],
                ],
                '/api/v1/app/auth/me' => [
                    'get' => ['summary' => 'Current user profile'],
                ],
                '/api/v1/app/tenant/current' => [
                    'get' => ['summary' => 'Current tenant'],
                ],
                '/api/v1/app/tenant/switch' => [
                    'post' => ['summary' => 'Switch tenant'],
                ],
                '/api/v1/app/capabilities' => [
                    'get' => ['summary' => 'Capabilities and feature flags'],
                ],
                '/api/v1/app/config' => [
                    'get' => ['summary' => 'App config and endpoints'],
                ],
                '/api/v1/app/sync/push' => [
                    'post' => ['summary' => 'Push outbox batch'],
                ],
                '/api/v1/app/sync/pull' => [
                    'get' => ['summary' => 'Pull sync changes'],
                ],
                '/api/v1/app/sync/conflicts' => [
                    'post' => ['summary' => 'Resolve conflicts'],
                ],
                '/api/v1/app/devices' => [
                    'post' => ['summary' => 'Register device'],
                ],
                '/api/v1/app/devices/{device}/tokens' => [
                    'post' => ['summary' => 'Register push token'],
                ],
                '/api/v1/app/devices/{device}' => [
                    'delete' => ['summary' => 'Revoke device'],
                ],
                '/api/v1/app/notifications' => [
                    'get' => ['summary' => 'In-app notifications feed'],
                ],
                '/api/v1/app/notifications/{notification}/read' => [
                    'post' => ['summary' => 'Mark notification read'],
                ],
                '/api/v1/app/realtime/signals' => [
                    'get' => ['summary' => 'Pull signaling messages'],
                    'post' => ['summary' => 'Send signaling message'],
                ],
                '/api/v1/app/tickets' => [
                    'get' => ['summary' => 'List support tickets'],
                    'post' => ['summary' => 'Create support ticket'],
                ],
                '/api/v1/app/tickets/{ticket}/messages' => [
                    'get' => ['summary' => 'List ticket messages'],
                    'post' => ['summary' => 'Create ticket message'],
                ],
                '/api/v1/app/tickets/{ticket}/attachments' => [
                    'post' => ['summary' => 'Upload ticket attachment'],
                ],
                '/api/v1/app/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Support;

use Illuminate\Support\Str;

class MailtrapFakeResponse
{
    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|null  $payload
     * @return array{status:int, body:array|string}
     */
    public static function handle(string $method, string $resource, array $query = [], ?array $payload = null): array
    {
        $runId = (string) (config('mailtrap-core.fake_run_id') ?: 'FAKE');
        $resource = ltrim($resource, '/');

        if (Str::startsWith($resource, 'accounts') && $resource === 'accounts') {
            return [
                'status' => 200,
                'body' => [
                    ['id' => 1, 'name' => 'Main-'.$runId],
                ],
            ];
        }

        if (Str::startsWith($resource, 'accounts/1/inboxes') && $resource === 'accounts/1/inboxes') {
            return [
                'status' => 200,
                'body' => [
                    [
                        'id' => 10,
                        'name' => 'Primary',
                        'status' => 'active',
                        'project_id' => 1,
                        'email_domain' => 'inbox.mailtrap.io',
                        'emails_count' => 5,
                        'emails_unread_count' => 2,
                    ],
                ],
            ];
        }

        if ($method === 'POST' && Str::startsWith($resource, 'accounts/1/projects/') && Str::endsWith($resource, '/inboxes')) {
            $inboxName = is_array($payload) ? ($payload['inbox']['name'] ?? 'Inbox') : 'Inbox';

            return [
                'status' => 201,
                'body' => [
                    'id' => 11,
                    'name' => $inboxName,
                    'status' => 'active',
                    'project_id' => 1,
                    'email_domain' => 'inbox.mailtrap.io',
                    'emails_count' => 0,
                    'emails_unread_count' => 0,
                ],
            ];
        }

        if (Str::startsWith($resource, 'accounts/1/sending_domains')) {
            return [
                'status' => 200,
                'body' => [
                    'data' => [
                        [
                            'id' => 55,
                            'domain_name' => 'example.com',
                            'dns_verified' => true,
                            'compliance_status' => 'ok',
                        ],
                    ],
                ],
            ];
        }

        if (Str::startsWith($resource, 'accounts/1/inboxes/10/messages') && $resource === 'accounts/1/inboxes/10/messages') {
            return [
                'status' => 200,
                'body' => [
                    'data' => [
                        [
                            'id' => 100,
                            'subject' => 'Welcome',
                            'from_email' => 'from@example.com',
                            'to_email' => 'to@example.com',
                            'is_read' => false,
                        ],
                    ],
                ],
            ];
        }

        if (Str::startsWith($resource, 'accounts/1/inboxes/10/messages/100')) {
            return [
                'status' => 200,
                'body' => [
                    'id' => 100,
                    'subject' => 'Welcome',
                    'from_email' => 'from@example.com',
                    'to_email' => 'to@example.com',
                    'is_read' => true,
                ],
            ];
        }

        return [
            'status' => 200,
            'body' => [],
        ];
    }
}

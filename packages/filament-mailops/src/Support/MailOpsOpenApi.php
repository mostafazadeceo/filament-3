<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Support;

class MailOpsOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'MailOps API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/filament-mailops/domains' => [
                    'get' => ['summary' => 'List domains'],
                    'post' => ['summary' => 'Create domain'],
                ],
                '/api/v1/filament-mailops/domains/{domain}' => [
                    'get' => ['summary' => 'Show domain'],
                    'patch' => ['summary' => 'Update domain'],
                    'delete' => ['summary' => 'Delete domain'],
                ],
                '/api/v1/filament-mailops/mailboxes' => [
                    'get' => ['summary' => 'List mailboxes'],
                    'post' => ['summary' => 'Create mailbox'],
                ],
                '/api/v1/filament-mailops/mailboxes/{mailbox}' => [
                    'get' => ['summary' => 'Show mailbox'],
                    'patch' => ['summary' => 'Update mailbox'],
                    'delete' => ['summary' => 'Delete mailbox'],
                ],
                '/api/v1/filament-mailops/aliases' => [
                    'get' => ['summary' => 'List aliases'],
                    'post' => ['summary' => 'Create alias'],
                ],
                '/api/v1/filament-mailops/aliases/{alias}' => [
                    'get' => ['summary' => 'Show alias'],
                    'patch' => ['summary' => 'Update alias'],
                    'delete' => ['summary' => 'Delete alias'],
                ],
                '/api/v1/filament-mailops/outbound-messages' => [
                    'get' => ['summary' => 'List outbound messages'],
                    'post' => ['summary' => 'Send outbound message'],
                ],
                '/api/v1/filament-mailops/outbound-messages/{message}' => [
                    'get' => ['summary' => 'Show outbound message'],
                ],
                '/api/v1/filament-mailops/inbound-messages' => [
                    'get' => ['summary' => 'List inbound messages'],
                ],
                '/api/v1/filament-mailops/inbound-messages/{message}' => [
                    'get' => ['summary' => 'Show inbound message'],
                ],
                '/api/v1/filament-mailops/inbound-messages/sync' => [
                    'post' => ['summary' => 'Sync inbox messages'],
                ],
                '/api/v1/filament-mailops/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

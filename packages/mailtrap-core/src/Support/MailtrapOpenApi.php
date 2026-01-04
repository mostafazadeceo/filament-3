<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Support;

class MailtrapOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Mailtrap Integration API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/mailtrap/connections' => [
                    'get' => ['summary' => 'List connections'],
                    'post' => ['summary' => 'Create connection'],
                ],
                '/api/v1/mailtrap/connections/{connection}' => [
                    'get' => ['summary' => 'Show connection'],
                    'put' => ['summary' => 'Update connection'],
                    'delete' => ['summary' => 'Delete connection'],
                ],
                '/api/v1/mailtrap/inboxes' => [
                    'get' => ['summary' => 'List inboxes'],
                    'post' => ['summary' => 'Create inbox'],
                ],
                '/api/v1/mailtrap/inboxes/{inbox}' => [
                    'put' => ['summary' => 'Update inbox'],
                    'delete' => ['summary' => 'Delete inbox'],
                ],
                '/api/v1/mailtrap/inboxes/sync' => [
                    'post' => ['summary' => 'Sync inboxes'],
                ],
                '/api/v1/mailtrap/messages' => [
                    'get' => ['summary' => 'List messages'],
                ],
                '/api/v1/mailtrap/messages/{message}' => [
                    'get' => ['summary' => 'Show message'],
                ],
                '/api/v1/mailtrap/messages/{message}/body' => [
                    'get' => ['summary' => 'Get message body'],
                ],
                '/api/v1/mailtrap/messages/{message}/attachments' => [
                    'get' => ['summary' => 'List message attachments'],
                ],
                '/api/v1/mailtrap/messages/{message}/attachments/{attachment}' => [
                    'get' => ['summary' => 'Download attachment'],
                ],
                '/api/v1/mailtrap/domains' => [
                    'get' => ['summary' => 'List sending domains'],
                    'post' => ['summary' => 'Create sending domain'],
                ],
                '/api/v1/mailtrap/domains/{domain}' => [
                    'put' => ['summary' => 'Update sending domain'],
                    'delete' => ['summary' => 'Delete sending domain'],
                ],
                '/api/v1/mailtrap/domains/sync' => [
                    'post' => ['summary' => 'Sync sending domains'],
                ],
                '/api/v1/mailtrap/offers' => [
                    'get' => ['summary' => 'List mailtrap offers'],
                    'post' => ['summary' => 'Create mailtrap offer'],
                ],
                '/api/v1/mailtrap/audiences' => [
                    'get' => ['summary' => 'List audiences'],
                    'post' => ['summary' => 'Create audience'],
                ],
                '/api/v1/mailtrap/audiences/{audience}' => [
                    'get' => ['summary' => 'Show audience'],
                    'put' => ['summary' => 'Update audience'],
                    'delete' => ['summary' => 'Delete audience'],
                ],
                '/api/v1/mailtrap/audiences/{audience}/contacts' => [
                    'get' => ['summary' => 'List audience contacts'],
                    'post' => ['summary' => 'Create audience contact'],
                ],
                '/api/v1/mailtrap/audiences/{audience}/contacts/{contact}' => [
                    'put' => ['summary' => 'Update audience contact'],
                    'delete' => ['summary' => 'Delete audience contact'],
                ],
                '/api/v1/mailtrap/campaigns' => [
                    'get' => ['summary' => 'List campaigns'],
                    'post' => ['summary' => 'Create campaign'],
                ],
                '/api/v1/mailtrap/campaigns/{campaign}' => [
                    'get' => ['summary' => 'Show campaign'],
                    'put' => ['summary' => 'Update campaign'],
                    'delete' => ['summary' => 'Delete campaign'],
                ],
                '/api/v1/mailtrap/campaigns/{campaign}/send' => [
                    'post' => ['summary' => 'Send campaign'],
                ],
                '/api/v1/mailtrap/single-sends' => [
                    'get' => ['summary' => 'List single sends'],
                    'post' => ['summary' => 'Send single email'],
                ],
                '/api/v1/mailtrap/single-sends/{single_send}' => [
                    'get' => ['summary' => 'Show single send'],
                ],
                '/api/v1/mailtrap/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}

<?php

namespace Haida\FilamentMeetings\Support;

class MeetingsOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Meetings API',
                'version' => '1.0.0',
            ],
            'servers' => [
                ['url' => url('/api/v1/meetings')],
            ],
            'paths' => [
                '/' => [
                    'get' => ['summary' => 'List meetings', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create meeting', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/{meeting}' => [
                    'get' => ['summary' => 'Get meeting', 'responses' => ['200' => ['description' => 'OK']]],
                    'put' => ['summary' => 'Update meeting', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete meeting', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
                '/templates' => [
                    'get' => ['summary' => 'List templates', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create template', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/templates/{template}' => [
                    'get' => ['summary' => 'Get template', 'responses' => ['200' => ['description' => 'OK']]],
                    'put' => ['summary' => 'Update template', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete template', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
                '/{meeting}/attendees' => [
                    'get' => ['summary' => 'List attendees', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create attendee', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/attendees/{attendee}' => [
                    'put' => ['summary' => 'Update attendee', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete attendee', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
                '/{meeting}/agenda-items' => [
                    'get' => ['summary' => 'List agenda items', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create agenda item', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/agenda-items/{agendaItem}' => [
                    'put' => ['summary' => 'Update agenda item', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete agenda item', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
                '/{meeting}/consent/confirm' => [
                    'post' => ['summary' => 'Confirm consent', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/{meeting}/transcript/upload' => [
                    'post' => ['summary' => 'Upload transcript', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/{meeting}/transcript/manual' => [
                    'post' => ['summary' => 'Submit manual transcript', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/{meeting}/ai/generate-agenda' => [
                    'post' => ['summary' => 'Generate agenda', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/{meeting}/ai/generate-minutes' => [
                    'post' => ['summary' => 'Generate minutes', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/{meeting}/ai/recap' => [
                    'post' => ['summary' => 'Generate recap', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/{meeting}/minutes/export' => [
                    'get' => ['summary' => 'Export minutes (markdown)', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/{meeting}/action-items/link-to-workhub' => [
                    'post' => ['summary' => 'Link action items to Workhub', 'responses' => ['200' => ['description' => 'OK']]],
                ],
            ],
        ];
    }
}

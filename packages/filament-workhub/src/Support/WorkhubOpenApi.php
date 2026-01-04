<?php

namespace Haida\FilamentWorkhub\Support;

class WorkhubOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Workhub API',
                'version' => '1.0.0',
            ],
            'servers' => [
                ['url' => url('/api/v1/workhub')],
            ],
            'paths' => [
                '/projects' => [
                    'get' => ['summary' => 'List projects', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create project', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/projects/{project}' => [
                    'get' => ['summary' => 'Get project', 'responses' => ['200' => ['description' => 'OK']]],
                    'put' => ['summary' => 'Update project', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete project', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
                '/work-items' => [
                    'get' => ['summary' => 'List work items', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create work item', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/work-items/{workItem}' => [
                    'get' => ['summary' => 'Get work item', 'responses' => ['200' => ['description' => 'OK']]],
                    'put' => ['summary' => 'Update work item', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete work item', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
                '/work-items/{workItem}/transition' => [
                    'post' => ['summary' => 'Transition work item', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/work-items/{workItem}/ai/personal-summary' => [
                    'post' => ['summary' => 'Generate personal summary', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/work-items/{workItem}/ai/shared-summary' => [
                    'post' => ['summary' => 'Generate shared summary', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/work-items/{workItem}/ai/thread-summary' => [
                    'post' => ['summary' => 'Summarize comment thread', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/work-items/{workItem}/ai/generate-subtasks' => [
                    'post' => ['summary' => 'Generate subtasks', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/work-items/{workItem}/ai/progress-update' => [
                    'post' => ['summary' => 'Generate progress update', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/work-items/{workItem}/ai/find-similar' => [
                    'post' => ['summary' => 'Find similar work items', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/projects/{project}/ai/executive-summary' => [
                    'post' => ['summary' => 'Generate executive summary', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/projects/{project}/ai/stuck-tasks' => [
                    'get' => ['summary' => 'List stuck tasks', 'responses' => ['200' => ['description' => 'OK']]],
                ],
                '/work-types' => [
                    'get' => ['summary' => 'List work types', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create work type', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/work-types/{workType}' => [
                    'get' => ['summary' => 'Get work type', 'responses' => ['200' => ['description' => 'OK']]],
                    'put' => ['summary' => 'Update work type', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete work type', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
                '/custom-fields' => [
                    'get' => ['summary' => 'List custom fields', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create custom field', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/custom-fields/{customField}' => [
                    'get' => ['summary' => 'Get custom field', 'responses' => ['200' => ['description' => 'OK']]],
                    'put' => ['summary' => 'Update custom field', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete custom field', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
                '/automation-rules' => [
                    'get' => ['summary' => 'List automation rules', 'responses' => ['200' => ['description' => 'OK']]],
                    'post' => ['summary' => 'Create automation rule', 'responses' => ['201' => ['description' => 'Created']]],
                ],
                '/automation-rules/{automationRule}' => [
                    'get' => ['summary' => 'Get automation rule', 'responses' => ['200' => ['description' => 'OK']]],
                    'put' => ['summary' => 'Update automation rule', 'responses' => ['200' => ['description' => 'OK']]],
                    'delete' => ['summary' => 'Delete automation rule', 'responses' => ['204' => ['description' => 'No Content']]],
                ],
            ],
        ];
    }
}

<?php

namespace Haida\FilamentNotify\Core\Resources\TemplateResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Haida\FilamentNotify\Core\Resources\TemplateResource;
use Haida\FilamentNotify\Core\Support\Rendering\TemplateRenderer;

class EditTemplate extends EditRecord
{
    protected static string $resource = TemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('پیش‌نمایش')
                ->form([
                    Textarea::make('context_json')
                        ->label('نمونه JSON')
                        ->rows(6)
                        ->placeholder('{"record":{"name":"نمونه"},"user":{"email":"test@example.com"}}')
                        ->default('{"record":{"name":"نمونه"},"user":{"email":"test@example.com"}}'),
                ])
                ->action(function (array $data): void {
                    $context = [];
                    if (! empty($data['context_json'])) {
                        $decoded = json_decode($data['context_json'], true);
                        if (is_array($decoded)) {
                            $context = $decoded;
                        }
                    }

                    $renderer = app(TemplateRenderer::class);
                    $record = $this->getRecord();
                    $rendered = $renderer->render($record->subject, $record->body, $context, $record->meta ?? []);

                    Notification::make()
                        ->title('پیش‌نمایش تولید شد')
                        ->body($rendered->body)
                        ->success()
                        ->send();
                }),
        ];
    }
}

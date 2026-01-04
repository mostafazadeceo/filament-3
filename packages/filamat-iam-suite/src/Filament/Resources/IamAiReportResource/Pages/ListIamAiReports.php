<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\IamAiReportResource\Pages;

use Filamat\IamSuite\Filament\Resources\IamAiReportResource;
use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;

class ListIamAiReports extends ListRecordsWithCreate
{
    protected static string $resource = IamAiReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            Action::make('exportCsv')
                ->label('خروجی CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => IamAuthorization::allows('automation.reports.view'))
                ->action(function () {
                    $records = $this->getTableQueryForExport()->get();

                    return response()->streamDownload(function () use ($records) {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, [
                            'id',
                            'tenant_id',
                            'title',
                            'severity',
                            'status',
                            'created_at',
                        ]);

                        foreach ($records as $record) {
                            fputcsv($handle, [
                                $record->getKey(),
                                $record->tenant_id,
                                $record->title,
                                $record->severity,
                                $record->status,
                                optional($record->created_at)->toDateTimeString(),
                            ]);
                        }

                        fclose($handle);
                    }, 'iam-ai-reports.csv');
                }),
            Action::make('exportJson')
                ->label('خروجی JSON')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => IamAuthorization::allows('automation.reports.view'))
                ->action(function () {
                    $records = $this->getTableQueryForExport()->get();

                    return response()->streamDownload(function () use ($records) {
                        echo json_encode($records->toArray(), JSON_UNESCAPED_UNICODE);
                    }, 'iam-ai-reports.json');
                }),
        ];
    }
}

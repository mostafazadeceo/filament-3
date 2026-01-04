<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamAiActionProposalResource\Pages\ListIamAiActionProposals;
use Filamat\IamSuite\Models\IamAiActionProposal;
use Filamat\IamSuite\Services\Automation\IamEventFactory;
use Filamat\IamSuite\Services\Automation\IamEventPublisher;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IamAiActionProposalResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'automation.actions';

    protected static ?string $model = IamAiActionProposal::class;

    protected static ?string $navigationLabel = 'پیشنهادهای اقدام';

    protected static ?string $pluralModelLabel = 'پیشنهادهای اقدام';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-command-line';

    protected static string|\UnitEnum|null $navigationGroup = 'اتوماسیون';

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('action_type')->label('نوع اقدام'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending' => 'در انتظار',
                        'approved' => 'تایید شد',
                        'rejected' => 'رد شد',
                        'executed' => 'اجرا شد',
                        default => $state,
                    }),
                IconColumn::make('requires_approval')->label('نیاز به تایید')->boolean(),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('تایید')
                    ->requiresConfirmation()
                    ->visible(fn (IamAiActionProposal $record) => $record->status === 'pending' && IamAuthorization::allows('automation.actions.manage'))
                    ->action(function (IamAiActionProposal $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_by_id' => auth()->id(),
                            'approved_at' => now(),
                        ]);

                        $factory = app(IamEventFactory::class);
                        $publisher = app(IamEventPublisher::class);
                        $publisher->publish($factory->fromAutomationProposalApproved($record->tenant_id, [
                            'id' => $record->getKey(),
                            'action_type' => $record->action_type,
                            'status' => $record->status,
                        ]));
                    }),
                Action::make('reject')
                    ->label('رد')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (IamAiActionProposal $record) => $record->status === 'pending' && IamAuthorization::allows('automation.actions.manage'))
                    ->action(function (IamAiActionProposal $record) {
                        $record->update([
                            'status' => 'rejected',
                            'rejected_by_id' => auth()->id(),
                            'rejected_at' => now(),
                        ]);

                        $factory = app(IamEventFactory::class);
                        $publisher = app(IamEventPublisher::class);
                        $publisher->publish($factory->fromAutomationProposalRejected($record->tenant_id, [
                            'id' => $record->getKey(),
                            'action_type' => $record->action_type,
                            'status' => $record->status,
                        ]));
                    }),
                Action::make('execute')
                    ->label('اجرا')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (IamAiActionProposal $record) => $record->status === 'approved' && IamAuthorization::allows('automation.actions.manage'))
                    ->action(function (IamAiActionProposal $record) {
                        $record->update([
                            'status' => 'executed',
                            'executed_by_id' => auth()->id(),
                            'executed_at' => now(),
                            'result' => ['status' => 'recorded'],
                        ]);

                        $factory = app(IamEventFactory::class);
                        $publisher = app(IamEventPublisher::class);
                        $publisher->publish($factory->fromAutomationProposalExecuted($record->tenant_id, [
                            'id' => $record->getKey(),
                            'action_type' => $record->action_type,
                            'status' => $record->status,
                        ]));
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIamAiActionProposals::route('/'),
        ];
    }
}

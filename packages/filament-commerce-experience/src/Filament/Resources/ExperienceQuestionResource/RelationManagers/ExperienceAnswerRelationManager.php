<?php

namespace Haida\FilamentCommerceExperience\Filament\Resources\ExperienceQuestionResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExperienceAnswerRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Textarea::make('answer')
                    ->label('پاسخ')
                    ->rows(3)
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'approved' => 'تایید شده',
                        'rejected' => 'رد شده',
                    ])
                    ->default('approved')
                    ->required(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('answer')
                    ->label('پاسخ')
                    ->limit(50),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $data['tenant_id'] ?? $this->getOwnerRecord()->tenant_id;
        $data['answered_by_user_id'] = auth()->id();

        return $data;
    }
}

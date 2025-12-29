<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $title = 'پیوست‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('path')
                    ->label('فایل')
                    ->disk('public')
                    ->directory('workhub/attachments')
                    ->preserveFilenames()
                    ->storeFileNamesIn('filename')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('filename')->label('نام فایل'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->defaultSort('created_at', 'desc');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $this->getOwnerRecord()->tenant_id;
        $data['user_id'] = auth()->id();
        $data['disk'] = $data['disk'] ?? 'public';

        $path = $data['path'] ?? null;
        if ($path && isset($data['disk'])) {
            $disk = $data['disk'];
            $data['mime_type'] = Storage::disk($disk)->mimeType($path) ?: null;
            $data['size'] = Storage::disk($disk)->size($path) ?: 0;
        }

        return $data;
    }
}

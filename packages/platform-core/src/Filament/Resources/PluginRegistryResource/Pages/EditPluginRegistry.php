<?php

namespace Haida\PlatformCore\Filament\Resources\PluginRegistryResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\PlatformCore\Filament\Resources\PluginRegistryResource;
use Haida\PlatformCore\Models\PluginRegistry;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class EditPluginRegistry extends EditRecord
{
    protected static string $resource = PluginRegistryResource::class;

    public function mount($record): void
    {
        $this->ensureRegistryTableExists();

        parent::mount($record);
    }

    protected function ensureRegistryTableExists(): void
    {
        $table = (new PluginRegistry())->getTable();
        if (! Schema::hasTable($table)) {
            throw new ServiceUnavailableHttpException(null, 'جدول رجیستری افزونه‌ها ایجاد نشده است. ابتدا مهاجرت‌ها را اجرا کنید.');
        }
    }
}

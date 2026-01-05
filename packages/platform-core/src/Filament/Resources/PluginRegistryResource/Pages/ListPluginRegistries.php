<?php

namespace Haida\PlatformCore\Filament\Resources\PluginRegistryResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Haida\PlatformCore\Filament\Resources\PluginRegistryResource;
use Haida\PlatformCore\Models\PluginRegistry;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class ListPluginRegistries extends ListRecordsWithCreate
{
    protected static string $resource = PluginRegistryResource::class;

    public function mount(): void
    {
        $this->ensureRegistryTableExists();

        parent::mount();
    }

    protected function ensureRegistryTableExists(): void
    {
        $table = (new PluginRegistry)->getTable();
        if (! Schema::hasTable($table)) {
            throw new ServiceUnavailableHttpException(null, 'جدول رجیستری افزونه‌ها ایجاد نشده است. ابتدا مهاجرت‌ها را اجرا کنید.');
        }
    }
}

<x-filament::page>
    <div class="space-y-6">
        {{ $this->form }}

        <x-filament::button wire:click="save">
            ذخیره تنظیمات
        </x-filament::button>
    </div>
</x-filament::page>

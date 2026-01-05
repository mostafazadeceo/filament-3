<x-filament::page>
    <div class="space-y-6">
        {{ $this->form }}

        <x-filament::button wire:click="create">
            ایجاد سازمان
        </x-filament::button>
    </div>
</x-filament::page>

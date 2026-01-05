<x-filament::page>
    @php
        $path = base_path('docs/_reference/WIKI/MEGA_SUPER_ADMIN.md');
        $content = file_exists($path) ? file_get_contents($path) : "# راهنما\n\nفایل راهنما یافت نشد.";
    @endphp

    <div class="prose max-w-none">
        {!! \Illuminate\Support\Str::markdown($content) !!}
    </div>
</x-filament::page>

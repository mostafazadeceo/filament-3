<x-filament::page>
    <div class="space-y-6">
        {{ $this->form }}

        <x-filament::button wire:click="simulate">
            بررسی دسترسی
        </x-filament::button>

        @if($result)
            <x-filament::section>
                <x-slot name="heading">
                    نتیجه
                </x-slot>

                <div class="space-y-2">
                    <div>
                        <strong>وضعیت:</strong>
                        {{ $result['allowed'] ? 'مجاز' : 'غیرمجاز' }}
                    </div>
                    <div>
                        <strong>ردیابی:</strong>
                        <ul class="list-disc ms-5">
                            @foreach($result['trace'] as $row)
                                @php
                                    $sourceMap = [
                                        'user_override' => 'بازنویسی کاربر',
                                        'group_permission' => 'مجوز گروه',
                                        'group_role' => 'نقش گروه',
                                        'role' => 'نقش',
                                        'subscription' => 'اشتراک',
                                        'default' => 'پیش‌فرض',
                                    ];
                                    $effectMap = [
                                        'allow' => 'اجازه',
                                        'deny' => 'عدم اجازه',
                                    ];
                                    $sourceLabel = $sourceMap[$row['source']] ?? $row['source'];
                                    $effectLabel = $effectMap[$row['effect']] ?? $row['effect'];
                                @endphp
                                <li>{{ $row['detail'] }} ({{ $sourceLabel }} / {{ $effectLabel }})</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament::page>

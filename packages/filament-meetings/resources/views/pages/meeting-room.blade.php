<x-filament::page>
    <div class="space-y-6">
        @if ($showAiBanner)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                هوش مصنوعی برای این جلسه فعال است. برای تولید صورتجلسه و خلاصه باید رضایت ثبت شود.
            </div>
        @endif

        <x-filament::section heading="اطلاعات جلسه">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">عنوان</div>
                    <div class="text-lg font-semibold">{{ $record->title }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">وضعیت</div>
                    <div class="text-lg font-semibold">
                        {{ [
                            'draft' => 'پیش‌نویس',
                            'scheduled' => 'برنامه‌ریزی شده',
                            'running' => 'در حال برگزاری',
                            'completed' => 'تکمیل شده',
                            'archived' => 'بایگانی شده',
                        ][$record->status] ?? $record->status }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">زمان شروع</div>
                    <div class="text-lg font-semibold">
                        {{ $record->scheduled_at?->format('Y-m-d H:i') ?? 'نامشخص' }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">برگزارکننده</div>
                    <div class="text-lg font-semibold">
                        {{ $record->organizer?->name ?? 'نامشخص' }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">مدت</div>
                    <div class="text-lg font-semibold">
                        {{ $record->duration_minutes ? $record->duration_minutes.' دقیقه' : 'نامشخص' }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">محل</div>
                    <div class="text-lg font-semibold">
                        {{ $record->location_value ?: ($record->location_type === 'online' ? 'آنلاین' : 'حضوری') }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section heading="رضایت و شفافیت">
            <div class="space-y-2 text-sm text-gray-700">
                <div>{{ $consentMessage }}</div>
                <div class="text-xs text-gray-500">اسکریپت صوتی: {{ $consentVoiceScript }}</div>
                <div>
                    وضعیت رضایت:
                    @if (! $consentRequired)
                        نیازی به رضایت ندارد.
                    @elseif ($consentSatisfied)
                        ثبت شده
                    @else
                        ثبت نشده
                    @endif
                </div>
                <div>نوع رضایت: {{ $consentMode === 'per_attendee' ? 'تأیید هر شرکت‌کننده' : 'تأیید برگزارکننده' }}</div>
            </div>
        </x-filament::section>

        <x-filament::section heading="حاضران">
            @if (empty($attendees))
                <div class="text-sm text-gray-500">هنوز شرکت‌کننده‌ای ثبت نشده است.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-right text-gray-600">
                                <th class="py-2">نام</th>
                                <th class="py-2">نقش</th>
                                <th class="py-2">وضعیت حضور</th>
                                <th class="py-2">رضایت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attendees as $row)
                                <tr class="border-t">
                                    <td class="py-2">{{ $row['name'] ?? '-' }}</td>
                                    <td class="py-2">{{ $row['role'] ?? '-' }}</td>
                                    <td class="py-2">{{ $row['status'] ?? '-' }}</td>
                                    <td class="py-2">{{ $row['consent_granted_at'] ? 'ثبت شده' : 'نامشخص' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </x-filament::section>

        <x-filament::section heading="دستور جلسه">
            @if (empty($agendaItems))
                <div class="text-sm text-gray-500">دستور جلسه‌ای ثبت نشده است.</div>
            @else
                <ol class="space-y-2 text-sm text-gray-700">
                    @foreach ($agendaItems as $item)
                        <li class="rounded-md border border-gray-200 p-3">
                            <div class="font-semibold">{{ $item['title'] }}</div>
                            @if (! empty($item['description']))
                                <div class="text-xs text-gray-500">{{ $item['description'] }}</div>
                            @endif
                            <div class="text-xs text-gray-500">
                                {{ $item['timebox_minutes'] ? $item['timebox_minutes'].' دقیقه' : 'بدون زمان‌بندی' }}
                                @if (! empty($item['owner']))
                                    • مالک: {{ $item['owner'] }}
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            @endif
        </x-filament::section>

        <x-filament::section heading="یادداشت‌ها">
            <div class="space-y-3">
                <textarea wire:model.defer="notesContent" rows="6" class="w-full rounded-lg border-gray-300 text-sm"></textarea>
                <x-filament::button wire:click="saveNotes">ذخیره یادداشت‌ها</x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section heading="متن جلسه">
            <div class="text-sm text-gray-700">
                تعداد نسخه‌ها: {{ $transcriptCount }}
                @if ($lastTranscriptAt)
                    <span class="text-xs text-gray-500">(آخرین ثبت: {{ $lastTranscriptAt }})</span>
                @endif
            </div>
        </x-filament::section>

        <x-filament::section heading="صورتجلسه هوشمند">
            @if ($latestMinutes)
                <div class="space-y-3 text-sm text-gray-700">
                    <div>
                        <div class="text-xs text-gray-500">خلاصه کلی</div>
                        <div>{{ $latestMinutes['overview_text'] ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">صورتجلسه</div>
                        <div class="prose max-w-none">{!! nl2br(e($latestMinutes['summary_markdown'] ?? '')) !!}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">تصمیم‌ها</div>
                        <div>{{ is_array($latestMinutes['decisions'] ?? null) ? implode('، ', $latestMinutes['decisions']) : '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500">ریسک‌ها</div>
                        <div>{{ is_array($latestMinutes['risks'] ?? null) ? implode('، ', $latestMinutes['risks']) : '-' }}</div>
                    </div>
                </div>
            @else
                <div class="text-sm text-gray-500">صورتجلسه‌ای ثبت نشده است.</div>
            @endif
        </x-filament::section>

        <x-filament::section heading="اقدام‌ها">
            @if (empty($actionItems))
                <div class="text-sm text-gray-500">اقدامی ثبت نشده است.</div>
            @else
                <div class="space-y-2 text-sm text-gray-700">
                    @foreach ($actionItems as $item)
                        <div class="rounded-md border border-gray-200 p-3">
                            <div class="font-semibold">{{ $item['title'] }}</div>
                            @if (! empty($item['description']))
                                <div class="text-xs text-gray-500">{{ $item['description'] }}</div>
                            @endif
                            <div class="text-xs text-gray-500">
                                وضعیت: {{ $item['status'] ?? '-' }}
                                @if (! empty($item['assignee']))
                                    • مسئول: {{ $item['assignee'] }}
                                @endif
                                @if (! empty($item['due_date']))
                                    • سررسید: {{ $item['due_date'] }}
                                @endif
                                @if (! empty($item['linked_workhub_item_id']))
                                    • لینک‌شده به تسک
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament::page>

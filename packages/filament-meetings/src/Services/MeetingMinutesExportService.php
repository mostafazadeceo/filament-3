<?php

namespace Haida\FilamentMeetings\Services;

use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingMinute;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MeetingMinutesExportService
{
    public function exportMarkdown(Meeting $meeting): StreamedResponse
    {
        $meeting->loadMissing(['organizer', 'attendees.user', 'agendaItems', 'minutes', 'actionItems.assignee']);

        $minute = $meeting->minutes->sortByDesc('created_at')->first();
        if (! $minute) {
            abort(404, 'صورتجلسه‌ای برای این جلسه موجود نیست.');
        }

        $markdown = $this->buildMarkdown($meeting, $minute);
        $filename = $this->buildFilename($meeting);

        return response()->streamDownload(function () use ($markdown) {
            echo $markdown;
        }, $filename);
    }

    protected function buildMarkdown(Meeting $meeting, MeetingMinute $minute): string
    {
        $lines = [];

        $lines[] = '# صورتجلسه';
        $lines[] = '';
        $lines[] = '## اطلاعات جلسه';
        $lines[] = '- عنوان: '.$meeting->title;
        if ($meeting->scheduled_at) {
            $lines[] = '- زمان: '.$meeting->scheduled_at->format('Y-m-d H:i');
        }
        if ($meeting->organizer?->name) {
            $lines[] = '- برگزارکننده: '.$meeting->organizer->name;
        }

        $attendees = $meeting->attendees
            ->map(fn ($attendee) => $attendee->name ?: $attendee->user?->name)
            ->filter()
            ->values()
            ->all();
        if ($attendees !== []) {
            $lines[] = '';
            $lines[] = '## حاضران';
            $lines = array_merge($lines, $this->renderList($attendees));
        }

        if ($meeting->agendaItems->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '## دستور جلسه';
            $lines = array_merge($lines, $meeting->agendaItems->sortBy('sort_order')->map(function ($item) {
                return '- '.$item->title;
            })->values()->all());
        }

        if ($minute->overview_text) {
            $lines[] = '';
            $lines[] = '## مرور کلی';
            $lines[] = $minute->overview_text;
        }

        if ($minute->summary_markdown) {
            $lines[] = '';
            $lines[] = '## خلاصه';
            $lines[] = $minute->summary_markdown;
        }

        $decisions = $this->normalizeList($minute->decisions_json);
        if ($decisions !== []) {
            $lines[] = '';
            $lines[] = '## تصمیم‌ها';
            $lines = array_merge($lines, $this->renderList($decisions));
        }

        $risks = $this->normalizeList($minute->risks_json);
        if ($risks !== []) {
            $lines[] = '';
            $lines[] = '## ریسک‌ها/گلوگاه‌ها';
            $lines = array_merge($lines, $this->renderList($risks));
        }

        if ($meeting->actionItems->isNotEmpty()) {
            $lines[] = '';
            $lines[] = '## اقدام‌ها';
            foreach ($meeting->actionItems as $item) {
                $label = $item->title;
                if ($item->assignee?->name) {
                    $label .= ' (مسئول: '.$item->assignee->name.')';
                }
                if ($item->due_date) {
                    $label .= ' - موعد: '.$item->due_date->toDateString();
                }
                if ($item->status) {
                    $label .= ' - وضعیت: '.$item->status;
                }
                $lines[] = '- '.$label;
                if ($item->description) {
                    $lines[] = '  - توضیح: '.Str::limit($item->description, 500);
                }
            }
        }

        return trim(implode("\n", $lines))."\n";
    }

    protected function buildFilename(Meeting $meeting): string
    {
        $slug = Str::slug($meeting->title, '-');
        if ($slug === '') {
            $slug = 'meeting-'.$meeting->getKey();
        }

        return sprintf('meeting-minutes-%s.md', $slug);
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeList(mixed $payload): array
    {
        if ($payload === null) {
            return [];
        }

        $items = Arr::wrap($payload);

        return collect($items)
            ->map(function ($item) {
                if (is_string($item)) {
                    return $item;
                }
                if (is_array($item)) {
                    return $item['title'] ?? $item['text'] ?? $item['name'] ?? null;
                }

                return null;
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $items
     * @return array<int, string>
     */
    protected function renderList(array $items): array
    {
        return array_map(fn (string $item) => '- '.$item, $items);
    }
}

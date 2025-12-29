<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TimeEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'minutes' => ['required', 'integer', 'min:1'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
        ];
    }
}

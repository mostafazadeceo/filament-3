<?php

namespace Haida\FilamentThreeCx\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThreeCxCrmJournalCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'direction' => ['nullable', 'string', 'max:32'],
            'from' => ['nullable', 'string', 'max:64'],
            'from_number' => ['nullable', 'string', 'max:64'],
            'to' => ['nullable', 'string', 'max:64'],
            'to_number' => ['nullable', 'string', 'max:64'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date'],
            'duration' => ['nullable', 'numeric'],
            'status' => ['nullable', 'string', 'max:64'],
            'external_id' => ['nullable', 'string', 'max:255'],
        ];
    }
}

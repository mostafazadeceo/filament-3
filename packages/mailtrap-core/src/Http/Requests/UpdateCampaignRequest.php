<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'connection_id' => ['sometimes', 'exists:mailtrap_connections,id'],
            'audience_id' => ['nullable', 'exists:mailtrap_audiences,id'],
            'name' => ['sometimes', 'string', 'max:190'],
            'subject' => ['sometimes', 'string', 'max:190'],
            'from_email' => ['nullable', 'email', 'max:190'],
            'from_name' => ['nullable', 'string', 'max:190'],
            'html_body' => ['nullable', 'string'],
            'text_body' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'scheduled', 'sending', 'sent', 'failed'])],
            'scheduled_at' => ['nullable', 'date'],
            'settings' => ['nullable', 'array'],
        ];
    }
}

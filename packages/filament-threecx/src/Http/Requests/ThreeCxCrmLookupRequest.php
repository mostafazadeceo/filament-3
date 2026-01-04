<?php

namespace Haida\FilamentThreeCx\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThreeCxCrmLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $data = $this->validated();
            if (! ($data['phone'] ?? null) && ! ($data['email'] ?? null)) {
                $validator->errors()->add('phone', 'شماره یا ایمیل الزامی است.');
            }
        });
    }
}

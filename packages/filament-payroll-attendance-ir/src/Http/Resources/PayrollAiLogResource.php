<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollAiLogResource extends JsonResource
{
    public function toArray($request): array
    {
        $payloads = [];
        if (config('filament-payroll-attendance-ir.ai.log_payloads', false)) {
            $payloads = [
                'input_payload' => $this->input_payload,
                'output_payload' => $this->output_payload,
            ];
        }

        return array_merge([
            'id' => $this->id,
            'company_id' => $this->company_id,
            'actor_id' => $this->actor_id,
            'report_type' => $this->report_type,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'provider' => $this->provider,
            'input_hash' => $this->input_hash,
            'response_summary' => $this->response_summary,
            'created_at' => $this->created_at,
        ], $payloads);
    }
}

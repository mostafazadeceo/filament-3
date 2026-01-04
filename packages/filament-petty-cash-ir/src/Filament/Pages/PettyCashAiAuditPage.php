<?php

namespace Haida\FilamentPettyCashIr\Filament\Pages;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Haida\FilamentPettyCashIr\Application\Services\PettyCashAiService;
use Haida\FilamentPettyCashIr\Models\PettyCashAiSuggestion;
use Haida\FilamentPettyCashIr\Models\PettyCashControlException;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;

class PettyCashAiAuditPage extends Page
{
    protected static ?string $navigationLabel = 'کنترل‌های مستمر';

    protected static ?string $title = 'کنترل‌های مستمر و استثناها';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

    protected static ?int $navigationSort = 15;

    protected string $view = 'filament-petty-cash-ir::pages.ai-audit';

    public ?int $fundId = null;

    /**
     * @var array<string, int>
     */
    public array $summary = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $exceptions = [];

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $anomalies = [];

    public static function canView(): bool
    {
        return IamAuthorization::allows('petty_cash.exceptions.view')
            || IamAuthorization::allows('petty_cash.ai.view_reports')
            || IamAuthorization::allows('petty_cash.ai.use');
    }

    public function mount(): void
    {
        $this->loadAudit();
    }

    public function updatedFundId(): void
    {
        $this->loadAudit();
    }

    public function runAudit(): void
    {
        $service = app(PettyCashAiService::class);
        $result = $service->runContinuousAudit(
            $this->fundId,
            (int) config('filament-petty-cash-ir.ai.max_scan', 200),
            auth()->id()
        );

        if (! ($result['enabled'] ?? false)) {
            Notification::make()
                ->title($result['message'] ?? 'هوش مصنوعی غیرفعال است.')
                ->danger()
                ->send();

            return;
        }

        $this->loadAudit();

        Notification::make()
            ->title('پایش هوشمند انجام شد.')
            ->success()
            ->send();
    }

    /**
     * @return array<int|string, string>
     */
    public function getFundOptions(): array
    {
        return PettyCashFund::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function loadAudit(): void
    {
        $exceptionsQuery = PettyCashControlException::query()->latest('detected_at');
        if ($this->fundId) {
            $exceptionsQuery->where('fund_id', $this->fundId);
        }

        $exceptions = $exceptionsQuery->limit(10)->get();

        $anomaliesQuery = PettyCashAiSuggestion::query()
            ->where('suggestion_type', 'anomaly_risk')
            ->latest('created_at')
            ->with('subject');
        if ($this->fundId) {
            $anomaliesQuery->where('fund_id', $this->fundId);
        }

        $anomalies = $anomaliesQuery->limit(10)->get();

        $this->summary = [
            'exceptions_open' => PettyCashControlException::query()
                ->when($this->fundId, fn ($query) => $query->where('fund_id', $this->fundId))
                ->where('status', '!=', 'resolved')
                ->count(),
            'ai_anomalies' => PettyCashAiSuggestion::query()
                ->when($this->fundId, fn ($query) => $query->where('fund_id', $this->fundId))
                ->where('suggestion_type', 'anomaly_risk')
                ->where('status', 'proposed')
                ->count(),
        ];

        $this->exceptions = $exceptions->map(function (PettyCashControlException $exception): array {
            return [
                'id' => $exception->getKey(),
                'title' => $exception->title,
                'severity' => $exception->severity,
                'status' => $exception->status,
                'fund' => $exception->fund?->name,
                'detected_at' => optional($exception->detected_at)->format('Y-m-d H:i'),
            ];
        })->toArray();

        $this->anomalies = $anomalies->map(function (PettyCashAiSuggestion $suggestion): array {
            $expense = $suggestion->subject instanceof PettyCashExpense ? $suggestion->subject : null;

            return [
                'id' => $suggestion->getKey(),
                'score' => $suggestion->score,
                'status' => $suggestion->status,
                'expense_reference' => $expense?->reference,
                'amount' => $expense?->amount,
                'date' => $expense?->expense_date?->format('Y-m-d'),
            ];
        })->toArray();
    }
}

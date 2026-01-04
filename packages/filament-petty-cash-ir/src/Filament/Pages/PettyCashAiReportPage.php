<?php

namespace Haida\FilamentPettyCashIr\Filament\Pages;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Pages\Page;
use Haida\FilamentPettyCashIr\Application\Services\PettyCashAiService;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;

class PettyCashAiReportPage extends Page
{
    protected static ?string $navigationLabel = 'گزارش مدیریتی هوشمند';

    protected static ?string $title = 'گزارش مدیریتی تنخواه';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

    protected static ?int $navigationSort = 16;

    protected string $view = 'filament-petty-cash-ir::pages.ai-report';

    public ?int $fundId = null;

    public int $rangeDays = 30;

    /**
     * @var array<string, mixed>
     */
    public array $report = [];

    public static function canView(): bool
    {
        return IamAuthorization::allows('petty_cash.ai.view_reports');
    }

    public function mount(): void
    {
        $this->loadReport();
    }

    public function updatedFundId(): void
    {
        $this->loadReport();
    }

    public function updatedRangeDays(): void
    {
        $this->loadReport();
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

    protected function loadReport(): void
    {
        $from = now()->subDays($this->rangeDays)->startOfDay();
        $to = now()->endOfDay();

        $this->report = app(PettyCashAiService::class)->buildManagementReport($this->fundId, $from, $to);
    }
}

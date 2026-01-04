<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Pages;

use Carbon\Carbon;
use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AiReportService;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\AttendanceReportService;
use Vendor\FilamentPayrollAttendanceIr\Filament\Widgets\AttendanceCoverageGapChartWidget;
use Vendor\FilamentPayrollAttendanceIr\Filament\Widgets\AttendanceWorkMinutesChartWidget;

class AttendanceManagementReportsPage extends Page implements HasForms
{
    use AuthorizesIam;
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'گزارش مدیریتی';

    protected static ?string $title = 'گزارش مدیریتی حضور و غیاب';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static string|\UnitEnum|null $navigationGroup = 'گزارش‌ها';

    protected static ?string $permission = 'payroll.report.view';

    protected string $view = 'filament-payroll-attendance-ir::pages.management-reports';

    public ?array $data = [];

    public array $summaryTotals = [];

    public array $coverageGaps = [];

    public array $aiReport = [];

    protected function getFooterWidgets(): array
    {
        return [
            AttendanceWorkMinutesChartWidget::class,
            AttendanceCoverageGapChartWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }

    public function mount(): void
    {
        $companyId = AccountingCompany::query()->value('id');

        $this->form->fill([
            'company_id' => $companyId,
            'branch_id' => null,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
        ]);

        $this->generateReport();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('فیلتر گزارش')
                    ->schema([
                        Select::make('company_id')
                            ->label('شرکت')
                            ->options(fn () => AccountingCompany::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        Select::make('branch_id')
                            ->label('شعبه')
                            ->options(fn () => AccountingBranch::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->nullable(),
                        DatePicker::make('period_start')
                            ->label('شروع بازه')
                            ->required(),
                        DatePicker::make('period_end')
                            ->label('پایان بازه')
                            ->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function generateReport(): void
    {
        $state = $this->form->getState();
        $companyId = (int) ($state['company_id'] ?? 0);
        if (! $companyId) {
            $this->summaryTotals = [];
            $this->coverageGaps = [];
            $this->aiReport = [];

            return;
        }

        $branchId = $state['branch_id'] ? (int) $state['branch_id'] : null;
        $start = $this->parseDate($state['period_start'] ?? now()->startOfMonth());
        $end = $this->parseDate($state['period_end'] ?? now()->endOfMonth());

        $reportService = app(AttendanceReportService::class);
        $summary = $reportService->timesheetSummary($companyId, $branchId, $start, $end);

        $this->summaryTotals = [
            'worked_minutes' => (int) $summary->sum('worked_minutes'),
            'overtime_minutes' => (int) $summary->sum('overtime_minutes'),
            'late_minutes' => (int) $summary->sum('late_minutes'),
            'absence_minutes' => (int) $summary->sum('absence_minutes'),
        ];

        $this->coverageGaps = $reportService
            ->coverageGapReport($companyId, $branchId, $start, $end)
            ->take(10)
            ->map(fn ($row) => [
                'work_date' => $row->work_date,
                'scheduled_count' => (int) $row->scheduled_count,
                'attended_count' => (int) $row->attended_count,
                'gap_count' => (int) $row->gap_count,
            ])
            ->toArray();

        $this->aiReport = app(AiReportService::class)->generatePersianManagerReport([
            'company_id' => $companyId,
            'branch_id' => $branchId,
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'summary_totals' => $this->summaryTotals,
        ]);
    }

    private function parseDate(mixed $value): Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        return Carbon::parse($value);
    }
}

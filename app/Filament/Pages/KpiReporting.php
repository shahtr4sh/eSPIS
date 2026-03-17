<?php

namespace App\Filament\Pages;

use App\Models\KpiPi;
use App\Models\ResponsibleOffice;
use App\Models\DataSource;
use App\Services\KpiReportingService;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Actions\Action;

class KpiReporting extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static string|\UnitEnum|null $navigationGroup = 'eSPIS Management';
    protected static ?string $navigationLabel = 'Reporting & Analytics';
    protected string $view = 'filament.pages.kpi-reporting';

    public ?string $year = null;
    public ?string $quarter = 'Q1';
    public ?string $type = null;
    public ?string $responsible_office_id = null;
    public ?string $dimension = null;

    public ?string $target_value_filter = null;
    public ?string $thrust_filter= null;
    public ?string $data_source_id = null;
    public ?string $distribution_type = null;
    public bool $missing_only = false;

    public array $summary = [];
    public array $rows = [];
    public array $charts = [];

    public function mount(): void
    {
        $this->year = (string) now()->year;
        $this->loadReport();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Report Filters')
                ->schema([
                    Grid::make(4)->schema([
                        Select::make('year')
                            ->options([
                                '2025' => '2025',
                                '2026' => '2026',
                                '2027' => '2027',
                                '2028' => '2028',
                                '2029' => '2029',
                                '2030' => '2030',
                            ])
                            ->live(),

                        Select::make('quarter')
                            ->label('Quarter')
                            ->options([
                                'Q1' => 'Q1',
                                'Q2' => 'Q2',
                                'Q3' => 'Q3',
                                'Q4' => 'Q4',
                            ])
                            ->default('Q1')
                            ->live(),

                        Select::make('type')
                            ->options([
                                'KPI' => 'KPI',
                                'PI' => 'PI',
                            ])
                            ->placeholder('All')
                            ->live(),

                        Select::make('responsible_office_id')
                            ->label('Responsible Office')
                            ->options(ResponsibleOffice::query()->pluck('name', 'id')->toArray())
                            ->placeholder('All')
                            ->searchable()
                            ->live(),

                        Select::make('dimension')
                            ->options(
                                KpiPi::query()
                                    ->whereNotNull('dimension')
                                    ->distinct()
                                    ->orderBy('dimension')
                                    ->pluck('dimension', 'dimension')
                                    ->toArray()
                            )
                            ->placeholder('All')
                            ->searchable()
                            ->live(),

                        Select::make('target_value_filter')
                            ->label('Target Value')
                            ->options([
                                'has_target' => 'Has Target Value',
                                'no_target' => 'No Target Value',
                            ])
                            ->placeholder('All')
                            ->live(),

                        Select::make('thrust_filter')
                            ->label('Thrust')
                            ->options([
                                '1' => '1',
                                '2' => '2',
                                '3' => '3',
                                '4' => '4',
                                '5' => '5',
                                '6' => '6',
                            ])
                            ->placeholder('All')
                            ->live(),

                        Select::make('data_source_id')
                            ->label('Source of Data')
                            ->options(DataSource::query()->pluck('name', 'id')->toArray())
                            ->placeholder('All')
                            ->searchable()
                            ->live(),

                        Toggle::make('missing_only')
                            ->label('Missing / Incomplete Only')
                            ->live(),
                    ]),
                ]),
        ]);
    }

    public function updated($property): void
    {
        $this->loadReport();
    }

    public function loadReport(): void
    {
        $service = app(KpiReportingService::class);

        $result = $service->generate([
            'year' => $this->year,
            'quarter' => $this->quarter,
            'type' => $this->type,
            'responsible_office_id' => $this->responsible_office_id,
            'dimension' => $this->dimension,
            'target_value_filter' => $this->target_value_filter,
            'thrust_filter' => $this->thrust_filter,
            'data_source_id' => $this->data_source_id,
            'missing_only' => $this->missing_only,
        ]);

        $this->summary = $result['summary'];
        $this->rows = $result['rows'];
        $this->charts = $result['charts'];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh')
                ->action(fn () => $this->loadReport()),

//            Action::make('print')
//                ->label('Print')
//                ->url(route('reports.kpi.print', [
//                    'year' => $this->year,
//                    'quarter' => $this->quarter,
//                    'type' => $this->type,
//                    'responsible_office_id' => $this->responsible_office_id,
//                    'dimension' => $this->dimension,
//                    'data_source_id' => $this->data_source_id,
//                    'distribution_type' => $this->distribution_type,
//                    'missing_only' => $this->missing_only ? 1 : 0,
//                ]), shouldOpenInNewTab: true),

//            Action::make('excel')
//                ->label('Export Excel')
//                ->url(route('reports.kpi.excel', [
//                    'year' => $this->year,
//                    'quarter' => $this->quarter,
//                    'type' => $this->type,
//                    'responsible_office_id' => $this->responsible_office_id,
//                    'dimension' => $this->dimension,
//                    'data_source_id' => $this->data_source_id,
//                    'distribution_type' => $this->distribution_type,
//                    'missing_only' => $this->missing_only ? 1 : 0,
//                ]), shouldOpenInNewTab: true),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\KpiPi;

class KpiReportingService
{
    public function generate(array $filters): array
    {
        $quarter = $filters['quarter'] ?? 'Q1';

        [$targetColumn, $achievementColumn] = $this->getQuarterColumns($quarter);

        $query = KpiPi::query()
            ->with(['responsibleOffice', 'dataSource']);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['responsible_office_id'])) {
            $query->where('responsible_office_id', $filters['responsible_office_id']);
        }

        if (!empty($filters['dimension'])) {
            $query->where('dimension', $filters['dimension']);
        }

        if (!empty($filters['thrust_filter'])) {
            $query->where('thrust', $filters['thrust_filter']);
        }

        if (!empty($filters['target_value_filter'])) {
            if ($filters['target_value_filter'] === 'has_target') {
                $query->whereNotNull('sasaran_tahunan')
                    ->where('sasaran_tahunan', '>', 0);
            }

            if ($filters['target_value_filter'] === 'no_target') {
                $query->where(function ($q) {
                    $q->whereNull('sasaran_tahunan')
                        ->orWhere('sasaran_tahunan', '<=', 0);
                });
            }
        }

        if (!empty($filters['data_source_id'])) {
            $query->where('data_source_id', $filters['data_source_id']);
        }

        if (!empty($filters['distribution_type'])) {
            $query->where('distribution_type', $filters['distribution_type']);
        }

        if (!empty($filters['missing_only'])) {
            $query->where(function ($q) use ($targetColumn, $achievementColumn) {
                $q->whereNull('sasaran_tahunan')
                    ->orWhereNull($targetColumn)
                    ->orWhereNull($achievementColumn);
            });
        }

        $records = $query->orderBy('code')->get();

        $rows = $records->values()->map(function ($record, $index) use ($targetColumn, $achievementColumn) {
            $annualTarget = $record->sasaran_tahunan;
            $annualAchievement = $record->pencapaian_tahunan;

            $annualCompletedPercent = null;
            if (!is_null($annualTarget) && (float) $annualTarget > 0 && !is_null($annualAchievement)) {
                $annualCompletedPercent = round(((float) $annualAchievement / (float) $annualTarget) * 100, 2);
            }

            return [
                'no' => $index + 1,
                'id' => $record->id,
                'code' => $record->code,
                'indicator' => $record->indicator,
                'dimension' => $record->dimension,
                'annual_target' => $annualTarget,
                'annual_completed_percent' => $annualCompletedPercent,
                'quarter_target' => $record->{$targetColumn},
                'quarter_achievement' => $record->{$achievementColumn},
                'data_source' => $record->dataSource->name ?? null,
                'evidence' => null,
            ];
        })->all();

        $summary = [
            'total' => count($rows),
            'kpi' => $query->clone()->where('type', 'KPI')->count(),
            'pi' => $query->clone()->where('type', 'PI')->count(),
            'missing_annual_target' => collect($rows)->filter(fn ($row) => is_null($row['annual_target']))->count(),
            'missing_quarter_target' => collect($rows)->filter(fn ($row) => is_null($row['quarter_target']))->count(),
            'missing_quarter_achievement' => collect($rows)->filter(fn ($row) => is_null($row['quarter_achievement']))->count(),
        ];

        $charts = [
            'by_dimension' => collect($records)
                ->groupBy('dimension')
                ->map(fn ($items) => count($items))
                ->sortKeys()
                ->all(),

            'by_data_source' => collect($records)
                ->groupBy(fn ($item) => $item->dataSource->name ?? 'N/A')
                ->map(fn ($items) => count($items))
                ->sortKeys()
                ->all(),
        ];

        return [
            'summary' => $summary,
            'rows' => $rows,
            'charts' => $charts,
            'quarter_label' => $quarter,
        ];
    }

    protected function getQuarterColumns(string $quarter): array
    {
        return match ($quarter) {
            'Q2' => ['sasaran_q2', 'pencapaian_q2'],
            'Q3' => ['sasaran_q3', 'pencapaian_q3'],
            'Q4' => ['sasaran_q4', 'pencapaian_q4'],
            default => ['sasaran_q1', 'pencapaian_q1'],
        };

    }

}


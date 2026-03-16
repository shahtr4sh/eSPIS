<?php

namespace App\Services;

use App\Models\KpiPi;

class KpiReportingService
{
    public function generate(array $filters): array
    {
        $quarter = $filters['quarter'] ?? 'ANNUAL';

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

        if (!empty($filters['data_source_id'])) {
            $query->where('data_source_id', $filters['data_source_id']);
        }

        if (!empty($filters['distribution_type'])) {
            $query->where('distribution_type', $filters['distribution_type']);
        }

        if (!empty($filters['missing_only'])) {
            $query->where(function ($q) use ($targetColumn, $achievementColumn) {
                $q->whereNull($targetColumn)
                    ->orWhereNull($achievementColumn);
            });
        }

        $records = $query->orderBy('code')->get();

        $rows = $records->map(function ($record) use ($targetColumn, $achievementColumn) {
            $target = $record->{$targetColumn};
            $achievement = $record->{$achievementColumn};

            $percent = null;
            if (!is_null($target) && (float) $target > 0 && !is_null($achievement)) {
                $percent = round(((float) $achievement / (float) $target) * 100, 2);
            }

            $status = $this->resolveStatus($target, $achievement, $percent);

            return [
                'id' => $record->id,
                'code' => $record->code,
                'type' => $record->type,
                'prime_objective' => $record->prime_objective,
                'dimension' => $record->dimension,
                'office' => $record->responsibleOffice->name ?? null,
                'source' => $record->dataSource->name ?? null,
                'distribution_type' => $record->distribution_type,
                'target' => $target,
                'achievement' => $achievement,
                'percent' => $percent,
                'status' => $status,
            ];
        })->values()->all();

        $summary = [
            'total' => count($rows),
            'kpi' => collect($rows)->where('type', 'KPI')->count(),
            'pi' => collect($rows)->where('type', 'PI')->count(),
            'complete' => collect($rows)->where('status', 'Complete')->count(),
            'incomplete' => collect($rows)->where('status', 'Incomplete')->count(),
            'achieved' => collect($rows)->where('status', 'Achieved')->count(),
            'underperforming' => collect($rows)->where('status', 'Underperforming')->count(),
            'no_target' => collect($rows)->where('status', 'No Target')->count(),
        ];

        $charts = [
            'by_type' => [
                'KPI' => $summary['kpi'],
                'PI' => $summary['pi'],
            ],
            'by_status' => [
                'Achieved' => $summary['achieved'],
                'Underperforming' => $summary['underperforming'],
                'Incomplete' => $summary['incomplete'],
                'No Target' => $summary['no_target'],
            ],
            'by_dimension' => collect($rows)
                ->groupBy('dimension')
                ->map(fn ($items) => count($items))
                ->sortKeys()
                ->all(),
            'by_office' => collect($rows)
                ->groupBy('office')
                ->map(fn ($items) => count($items))
                ->sortKeys()
                ->all(),
        ];

        return [
            'summary' => $summary,
            'rows' => $rows,
            'charts' => $charts,
        ];
    }

    protected function getQuarterColumns(string $quarter): array
    {
        return match ($quarter) {
            'Q1' => ['sasaran_q1', 'pencapaian_q1'],
            'Q2' => ['sasaran_q2', 'pencapaian_q2'],
            'Q3' => ['sasaran_q3', 'pencapaian_q3'],
            'Q4' => ['sasaran_q4', 'pencapaian_q4'],
            'ANNUAL' => ['sasaran_tahunan', 'pencapaian_tahunan'],
            default => ['sasaran_tahunan', 'pencapaian_tahunan'],
        };
    }

    protected function resolveStatus($target, $achievement, $percent): string
    {
        if (is_null($target) || is_null($achievement)) {
            return 'Incomplete';
        }

        if ((float) $target <= 0) {
            return 'No Target';
        }

        if (!is_null($percent) && $percent >= 100) {
            return 'Achieved';
        }

        return 'Underperforming';
    }
}

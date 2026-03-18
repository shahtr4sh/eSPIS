<?php

namespace App\Services;

use App\Models\KpiPi;

class KpiReportingService
{
    public function generate(array $filters): array
    {
        $quarter = $filters['quarter'] ?? 'Q1';

        $query = KpiPi::query()
            ->with([
                'responsibleOffice',
                'dataSource',
                'distributionWeights.distributionUnit',
                'distributionDetails.distributionUnit',
            ]);

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
                $query->where(function ($q) {
                    $q->whereNotNull('sasaran_tahunan')
                        ->where('sasaran_tahunan', '>', 0);
                });
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

        $records = $query->orderBy('code')->get();

        $summaryRows = [];
        $detailRows = [];

        foreach ($records as $index => $record) {
            $quarterTarget = $this->getQuarterTarget($record, $quarter);
            $quarterAchievement = $this->getQuarterAchievement($record, $quarter);

            $annualTarget = $record->sasaran_tahunan;
            $annualAchievement = $record->pencapaian_tahunan;

            if (is_null($annualTarget) && !is_null($record->annual_target)) {
                $annualTarget = $record->annual_target;
            }

            $annualCompletedPercent = null;
            if (!is_null($annualTarget) && (float) $annualTarget > 0 && !is_null($annualAchievement)) {
                $annualCompletedPercent = round(((float) $annualAchievement / (float) $annualTarget) * 100, 2);
            }

            $summaryRow = [
                'no' => $index + 1,
                'id' => $record->id,
                'type' => $record->type,
                'code' => $record->code,
                'indicator' => $record->indicator,
                'dimension' => $record->dimension,
                'annual_target' => $annualTarget,
                'annual_achievement' => $annualAchievement,
                'annual_completed_percent' => $annualCompletedPercent,
                'quarter_target' => $quarterTarget,
                'quarter_achievement' => $quarterAchievement,
                'data_source' => $record->dataSource->name ?? null,
                'evidence' => !empty($record->attachment_path) ? asset('storage/' . $record->attachment_path) : null,
            ];

            if ($this->passesMissingOnlyFilter($summaryRow, $filters['missing_only'] ?? false)) {
                $summaryRows[] = $summaryRow;

                foreach ($this->buildDetailRows($record, $quarter) as $detailRow) {
                    $detailRows[] = $detailRow;
                }
            }
        }

        $summary = [
            'total' => count($summaryRows),
            'kpi' => collect($summaryRows)->where('type', 'KPI')->count(),
            'pi' => collect($summaryRows)->where('type', 'PI')->count(),
            'missing_annual_target' => collect($summaryRows)->filter(
                fn ($row) => is_null($row['annual_target']) || (float) $row['annual_target'] <= 0
            )->count(),
            'missing_quarter_target' => collect($summaryRows)->filter(
                fn ($row) => is_null($row['quarter_target']) || (float) $row['quarter_target'] <= 0
            )->count(),
            'missing_quarter_achievement' => collect($summaryRows)->filter(
                fn ($row) => is_null($row['quarter_achievement'])
            )->count(),
        ];

        $charts = [
            'by_dimension' => collect($summaryRows)
                ->groupBy(fn ($row) => $row['dimension'] ?: 'N/A')
                ->map(fn ($items) => count($items))
                ->sortKeys()
                ->all(),

            'by_data_source' => collect($summaryRows)
                ->groupBy(fn ($row) => $row['data_source'] ?: 'N/A')
                ->map(fn ($items) => count($items))
                ->sortKeys()
                ->all(),

            'by_unit' => collect($detailRows)
                ->groupBy(fn ($row) => $row['unit'] ?: 'N/A')
                ->map(fn ($items) => count($items))
                ->sortKeys()
                ->all(),
        ];

        return [
            'summary' => $summary,
            'rows' => $summaryRows,
            'detail_rows' => $detailRows,
            'charts' => $charts,
            'quarter_label' => $quarter,
        ];
    }

    protected function passesMissingOnlyFilter(array $summaryRow, bool $missingOnly): bool
    {
        if (!$missingOnly) {
            return true;
        }

        return is_null($summaryRow['annual_target'])
            || is_null($summaryRow['quarter_target'])
            || is_null($summaryRow['quarter_achievement']);
    }

    protected function getQuarterTarget(KpiPi $record, string $quarter): ?float
    {
        $detailSum = $record->distributionDetails
            ->where('quarter', $quarter)
            ->sum(function ($row) {
                return is_null($row->target_value) ? 0 : (float) $row->target_value;
            });

        if ($record->distributionDetails->where('quarter', $quarter)->count() > 0) {
            return round((float) $detailSum, 2);
        }

        return $this->getParentQuarterTarget($record, $quarter);
    }

    protected function getQuarterAchievement(KpiPi $record, string $quarter): ?float
    {
        $detailRows = $record->distributionDetails->where('quarter', $quarter);

        if ($detailRows->count() > 0) {
            return round((float) $detailRows->sum(function ($row) {
                return is_null($row->achievement_value) ? 0 : (float) $row->achievement_value;
            }), 2);
        }

        return match ($quarter) {
            'Q2' => !is_null($record->pencapaian_q2) ? (float) $record->pencapaian_q2 : null,
            'Q3' => !is_null($record->pencapaian_q3) ? (float) $record->pencapaian_q3 : null,
            'Q4' => !is_null($record->pencapaian_q4) ? (float) $record->pencapaian_q4 : null,
            default => !is_null($record->pencapaian_q1) ? (float) $record->pencapaian_q1 : null,
        };
    }

    protected function buildDetailRows(KpiPi $record, string $quarter): array
    {
        $rows = [];

        $detailRows = $record->distributionDetails
            ->where('quarter', $quarter)
            ->values();

        foreach ($detailRows as $detail) {
            $target = !is_null($detail->target_value) ? (float) $detail->target_value : null;
            $achievement = !is_null($detail->achievement_value) ? (float) $detail->achievement_value : null;

            $rows[] = [
                'kpi_pi_id' => $record->id,
                'code' => $record->code,
                'indicator' => $record->indicator,
                'unit' => $detail->distributionUnit->code ?? '-',
                'quarter' => $quarter,
                'unit_target' => $target,
                'unit_achievement' => $achievement,
                'completion_percent' => (!is_null($target) && $target > 0 && !is_null($achievement))
                    ? round(($achievement / $target) * 100, 2)
                    : null,
            ];
        }

        return $rows;
    }



    protected function getParentQuarterTarget(KpiPi $record, string $quarter): ?float
    {
        return match ($quarter) {
            'Q2' => !is_null($record->sasaran_q2) ? (float) $record->sasaran_q2 : null,
            'Q3' => !is_null($record->sasaran_q3) ? (float) $record->sasaran_q3 : null,
            'Q4' => !is_null($record->sasaran_q4) ? (float) $record->sasaran_q4 : null,
            default => !is_null($record->sasaran_q1) ? (float) $record->sasaran_q1 : null,
        };
    }
}

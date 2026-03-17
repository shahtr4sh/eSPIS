<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiPi;
use App\Models\DistributionUnit;
use App\Models\KpiPiDistributionQuarterAchievement;
use App\Models\KpiPiDistributionWeight;

class KpiPiDistributionQuarterAchievementSeeder extends Seeder
{
    public function run(): void
    {
        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

        $kpis = KpiPi::with('distributionWeights')->get();

        foreach ($kpis as $kpi) {
            $weights = $kpi->distributionWeights->keyBy('distribution_unit_id');
            $totalWeight = (float) $kpi->distributionWeights->sum('weight_value');

            if ($totalWeight <= 0) {
                continue;
            }

            foreach ($quarters as $quarter) {
                $parentAchievement = match ($quarter) {
                    'Q1' => (float) ($kpi->pencapaian_q1 ?? 0),
                    'Q2' => (float) ($kpi->pencapaian_q2 ?? 0),
                    'Q3' => (float) ($kpi->pencapaian_q3 ?? 0),
                    'Q4' => (float) ($kpi->pencapaian_q4 ?? 0),
                    default => 0,
                };

                foreach ($weights as $distributionUnitId => $weightRow) {
                    $weightValue = (float) ($weightRow->weight_value ?? 0);

                    $achievementValue = $parentAchievement > 0
                        ? round($parentAchievement * ($weightValue / $totalWeight), 2)
                        : null;

                    KpiPiDistributionQuarterAchievement::updateOrCreate(
                        [
                            'kpi_pi_id' => $kpi->id,
                            'distribution_unit_id' => $distributionUnitId,
                            'quarter' => $quarter,
                        ],
                        [
                            'achievement_value' => $achievementValue,
                        ]
                    );
                }
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiPi;
use App\Models\DistributionUnit;
use App\Models\KpiPiDistributionDetail;

class KpiPiDistributionDetailSeeder extends Seeder
{
    public function run(): void
    {
        $quarters = ['Q1', 'Q2', 'Q3', 'Q4'];

        $units = DistributionUnit::orderBy('sort_order')->get();
        $kpis = KpiPi::all();

        foreach ($kpis as $kpi) {
            foreach ($quarters as $quarter) {
                foreach ($units as $unit) {
                    [$targetValue, $achievementValue] = $this->generateValues($kpi, $quarter, $unit->code);

                    KpiPiDistributionDetail::updateOrCreate(
                        [
                            'kpi_pi_id' => $kpi->id,
                            'distribution_unit_id' => $unit->id,
                            'quarter' => $quarter,
                        ],
                        [
                            'target_value' => $targetValue,
                            'achievement_value' => $achievementValue,
                        ]
                    );
                }
            }
        }
    }

    private function generateValues(KpiPi $kpi, string $quarter, string $unitCode): array
    {
        $baseTarget = match ($quarter) {
            'Q1' => (float) ($kpi->sasaran_q1 ?? 0),
            'Q2' => (float) ($kpi->sasaran_q2 ?? 0),
            'Q3' => (float) ($kpi->sasaran_q3 ?? 0),
            'Q4' => (float) ($kpi->sasaran_q4 ?? 0),
            default => 0,
        };

        $baseAchievement = match ($quarter) {
            'Q1' => (float) ($kpi->pencapaian_q1 ?? 0),
            'Q2' => (float) ($kpi->pencapaian_q2 ?? 0),
            'Q3' => (float) ($kpi->pencapaian_q3 ?? 0),
            'Q4' => (float) ($kpi->pencapaian_q4 ?? 0),
            default => 0,
        };

        $distributionPercent = [
            'KUBRA' => 0.20,
            'KWISH' => 0.15,
            'KSU' => 0.20,
            'KPSK' => 0.10,
            'KAUSAS' => 0.35,
        ];

        $percent = $distributionPercent[$unitCode] ?? 0;

        if ($baseTarget > 0) {
            $targetValue = round($baseTarget * $percent, 2);
        } else {
            $targetValue = fake()->numberBetween(5, 50);
        }

        if ($baseAchievement > 0) {
            $achievementValue = round($baseAchievement * $percent, 2);
        } else {
            $achievementValue = round($targetValue * fake()->randomFloat(2, 0.60, 1.10), 2);
        }

        return [$targetValue, $achievementValue];
    }
}

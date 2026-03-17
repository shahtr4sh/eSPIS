<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiPi;
use App\Models\DistributionUnit;
use App\Models\KpiPiDistributionWeight;

class KpiPiDistributionWeightSeeder extends Seeder
{
    public function run(): void
    {
        $defaultWeights = [
            'KUBRA' => 8,
            'KWISH' => 4,
            'KSU' => 8,
            'KPSK' => 0,
            'KAUSAS' => 5,
        ];

        $units = DistributionUnit::all()->keyBy('code');
        $kpis = KpiPi::all();

        foreach ($kpis as $kpi) {
            foreach ($defaultWeights as $unitCode => $weightValue) {
                if (!isset($units[$unitCode])) {
                    continue;
                }

                KpiPiDistributionWeight::updateOrCreate(
                    [
                        'kpi_pi_id' => $kpi->id,
                        'distribution_unit_id' => $units[$unitCode]->id,
                    ],
                    [
                        'weight_value' => $weightValue,
                    ]
                );
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DistributionUnit;

class DistributionUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['code' => 'KUBRA',  'name' => 'KUBRA',  'sort_order' => 1],
            ['code' => 'KWISH',  'name' => 'KWISH',  'sort_order' => 2],
            ['code' => 'KSU',    'name' => 'KSU',    'sort_order' => 3],
            ['code' => 'KPSK',   'name' => 'KPSK',   'sort_order' => 4],
            ['code' => 'KAUSAS', 'name' => 'KAUSAS', 'sort_order' => 5],
        ];

        foreach ($units as $unit) {
            DistributionUnit::updateOrCreate(
                ['code' => $unit['code']],
                $unit
            );
        }
    }
}

<x-filament-panels::page>
    @php
        $record = $this->getRecord();

        $units = \App\Models\DistributionUnit::orderBy('sort_order')->get();

        $weightMap = $record->distributionWeights
            ->load('distributionUnit')
            ->keyBy(fn ($row) => $row->distributionUnit->code ?? '');

        $achievementMap = $record->distributionQuarterAchievements
            ->load('distributionUnit')
            ->groupBy('quarter')
            ->map(function ($items) {
                return $items->keyBy(fn ($row) => $row->distributionUnit->code ?? '');
            });

        $totalWeight = (float) $record->distributionWeights->sum('weight_value');

        $quarterTargets = [
            'Q1' => (float) ($record->sasaran_q1 ?? 0),
            'Q2' => (float) ($record->sasaran_q2 ?? 0),
            'Q3' => (float) ($record->sasaran_q3 ?? 0),
            'Q4' => (float) ($record->sasaran_q4 ?? 0),
        ];

        $typeLabel = $record->type ?? '-';
        $kpiLabel = strtoupper($typeLabel) === 'PI' ? 'PI' : 'KPI';

        $thrustText = match((string) $record->thrust) {
            '1' => 'TERAS 1 - KECEMERLANGAN AKADEMIK DAN PENGANTARABANGSAAN',
            '2' => 'TERAS 2 - MEMPERKASAKAN PENYELIDIKAN DAN PENERBITAN BERIMPAK TINGGI',
            '3' => 'TERAS 3 - KECEMERLANGAN PELAJAR SECARA HOLISTIK',
            '4' => 'TERAS 4 - KELESTARIAN INSTITUSI',
            '5' => 'TERAS 5 - KEMAMPANAN KEWANGAN DAN SUMBER PENDAPATAN',
            '6' => 'TERAS 6 - TADBIR URUS YANG BAIK',
            default => 'TERAS ' . ($record->thrust ?? '-'),
        };
    @endphp

    <style>
        .kpi-sheet-wrapper {
            display: flex;
            justify-content: center;
        }

        .kpi-sheet {
            width: 100%;
            max-width: 1100px;
            background: #fff;
            border: 1px solid #000;
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
        }

        .kpi-sheet-header {
            text-align: center;
            font-weight: 700;
            padding: 16px 12px 10px;
            line-height: 1.35;
            font-size: 16px;
            text-transform: uppercase;
        }

        .kpi-sheet table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .kpi-sheet td,
        .kpi-sheet th {
            border: 1px solid #000;
            padding: 10px 12px;
            vertical-align: top;
            font-size: 14px;
            line-height: 1.35;
        }

        .kpi-label {
            width: 16%;
            font-weight: 700;
            text-transform: uppercase;
            background: #f5f5f5;
        }

        .kpi-value {
            width: 54%;
        }

        .kpi-code-box {
            width: 30%;
            text-align: center;
            vertical-align: middle !important;
            font-weight: 700;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .kpi-multiline {
            white-space: pre-line;
        }

        .kpi-quarters {
            background: #6b21a8;
            color: #fff;
        }

        @media (max-width: 768px) {
            .kpi-sheet td,
            .kpi-sheet th {
                font-size: 12px;
                padding: 8px;
            }

            .kpi-code-box {
                font-size: 18px;
            }
        }
    </style>

    <div class="kpi-sheet-wrapper">
        <div class="kpi-sheet">
            <div class="kpi-sheet-header">
                <div>{{ $thrustText }}</div>
            </div>

            <table>
                <tr>
                    <td class="kpi-label">PRIME OBJECTIVE</td>
                    <td class="kpi-value">{{ $record->prime_objective ?: '-' }}</td>
                    <td class="kpi-code-box" rowspan="2">{{ $record->code ?: '-' }}</td>
                </tr>

                <tr>
                    <td class="kpi-label">STRATEGY</td>
                    <td class="kpi-value kpi-multiline">{{ $record->strategy ?: '-' }}</td>
                </tr>

                <tr>
                    <td class="kpi-label">DIMENSION</td>
                    <td colspan="2">{{ $record->dimension ?: '-' }}</td>
                </tr>

                <tr>
                    <td class="kpi-label">{{ $kpiLabel }}</td>
                    <td colspan="2">{{ $record->indicator ?: '-' }}</td>
                </tr>

                <tr>
                    <td class="kpi-label">REFERENCE</td>
                    <td colspan="2">{{ $record->reference ?: '-' }}</td>
                </tr>

                <tr>
                    <td class="kpi-label">OD</td>
                    <td colspan="2" class="kpi-multiline">{{ $record->operational_definition ?: '-' }}</td>
                </tr>

                <tr>
                    <td class="kpi-label">SOURCE OF DATA</td>
                    <td colspan="2">{{ $record->dataSource->name ?? '-' }}</td>
                </tr>

                <tr>
                    <td class="kpi-label">DISTRIBUTION TYPE</td>
                    <td colspan="2" class="kpi-multiline">{{ $record->distribution_type ?: '-' }}</td>
                </tr>

                <tr>
                    <td class="kpi-label">RESPONSIBLE OFFICE</td>
                    <td colspan="2">{{ $record->responsibleOffice->name ?? '-' }}</td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse; margin-top: 18px; table-layout: fixed;">
                @foreach(['Q1','Q2','Q3','Q4'] as $quarter)
                    <tr>
                        <td class="kpi-quarters" colspan="{{ 1 + $units->count() }}"
                            style="border: 1px solid #000; text-align: center; font-weight: 700; padding: 10px 8px;">
                            {{ $quarter }}
                        </td>
                    </tr>

                    <tr>
                        <td style="background: #0a0a0a; border: 1px solid #000; padding: 8px;"></td>

                        @foreach($units as $unit)
                            <td class="kpi-label"
                                style="border: 1px solid #000; padding: 8px; text-align: center; font-weight: 700;">
                                {{ $unit->code }}
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="kpi-label"
                            style="border: 1px solid #000; padding: 8px; font-weight: 700;">
                            TARGET
                        </td>

                        @foreach($units as $unit)
                            @php
                                $weightValue = (float) ($weightMap[$unit->code]->weight_value ?? 0);
                                $parentQuarterTarget = $quarterTargets[$quarter] ?? 0;

                                $calculatedTarget = ($totalWeight > 0)
                                    ? round($parentQuarterTarget * ($weightValue / $totalWeight), 2)
                                    : null;
                            @endphp

                            <td style="border: 1px solid #000; padding: 8px;">
                                {{ is_null($calculatedTarget) ? '-' : number_format($calculatedTarget, 2) }}
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td class="kpi-label"
                            style="border: 1px solid #000; padding: 8px; font-weight: 700;">
                            ACHIEVEMENT
                        </td>

                        @foreach($units as $unit)
                            <td style="border: 1px solid #000; padding: 8px;">
                                {{ isset($achievementMap[$quarter][$unit->code]) ? number_format((float) $achievementMap[$quarter][$unit->code]->achievement_value, 2) : '-' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
</x-filament-panels::page>

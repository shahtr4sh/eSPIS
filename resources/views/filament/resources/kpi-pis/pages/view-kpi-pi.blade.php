<x-filament-panels::page>
    @php
        $record = $this->getRecord();

        $units = \App\Models\DistributionUnit::orderBy('sort_order')->get();

        $detailMap = $record->distributionDetails
            ->load('distributionUnit')
            ->groupBy('quarter')
            ->map(function ($items) {
                return $items->keyBy(fn ($row) => $row->distributionUnit->code ?? '');
            });

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

        $targetInputMode = $record->target_input_mode ?? 'annual_overall';

        // Untuk mode unit_quarterly, annual display guna Q4 sebab cumulative
        $annualQuarter = 'Q4';

        $annualOverallTarget = $targetInputMode === 'unit_quarterly'
            ? collect($units)->sum(function ($unit) use ($detailMap, $annualQuarter) {
                return (float) ($detailMap[$annualQuarter][$unit->code]->target_value ?? 0);
            })
            : (float) ($record->sasaran_tahunan ?? 0);

        $annualOverallAchievement = $targetInputMode === 'unit_quarterly'
            ? collect($units)->sum(function ($unit) use ($detailMap, $annualQuarter) {
                return (float) ($detailMap[$annualQuarter][$unit->code]->achievement_value ?? 0);
            })
            : (float) ($record->pencapaian_tahunan ?? 0);
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
                                $detailTarget = isset($detailMap[$quarter][$unit->code])
                                ? (float) $detailMap[$quarter][$unit->code]->target_value
                                : null;
                            @endphp

                            <td style="border: 1px solid #000; padding: 8px;">
                                {{ is_null($detailTarget) ? '-' : number_format($detailTarget, 2) }}
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
                                {{ isset($detailMap[$quarter][$unit->code]) ? number_format((float) $detailMap[$quarter][$unit->code]->achievement_value, 2) : '-' }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>

            <table style="width: 100%; border-collapse: collapse; margin-top: 18px; table-layout: fixed;">
                <tr>
                    <td class="kpi-quarters" colspan="{{ 1 + $units->count() }}"
                        style="border: 1px solid #000; text-align: center; font-weight: 700; padding: 10px 8px;">
                        ANNUAL
                        @if($targetInputMode === 'unit_quarterly')
                            (DISPLAY BASED ON Q4 CUMULATIVE)
                        @endif
                    </td>
                </tr>

                <tr>
                    <td class="kpi-label"
                        style="border: 1px solid #000; padding: 8px; font-weight: 700;">
                        OVERALL ANNUAL TARGET
                    </td>
                    <td colspan="{{ $units->count() }}" style="border: 1px solid #000; padding: 8px; font-weight: 600;">
                        {{ $annualOverallTarget > 0 ? number_format($annualOverallTarget, 2) : '-' }}
                    </td>
                </tr>

                <tr>
                    <td class="kpi-label"
                        style="border: 1px solid #000; padding: 8px; font-weight: 700;">
                        OVERALL ANNUAL ACHIEVEMENT
                    </td>
                    <td colspan="{{ $units->count() }}" style="border: 1px solid #000; padding: 8px; font-weight: 600;">
                        {{ $annualOverallAchievement > 0 ? number_format($annualOverallAchievement, 2) : '-' }}
                    </td>
                </tr>
            </table>

        </div>
    </div>
</x-filament-panels::page>

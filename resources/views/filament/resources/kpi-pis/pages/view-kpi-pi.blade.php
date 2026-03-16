<x-filament-panels::page>
    @php
        $record = $this->getRecord();

        $typeLabel = $record->type ?? '-';
        $kpiLabel = strtoupper($typeLabel) === 'PI' ? 'PI' : 'KPI';

        $thrustText = match((string) $record->thrust) {
            '1' => 'THRUST 1: ACADEMIC EXCELLENCE',
            '2' => 'THRUST 2',
            '3' => 'THRUST 3',
            '4' => 'THRUST 4',
            '5' => 'THRUST 5',
            '6' => 'THRUST 6',
            default => 'THRUST ' . ($record->thrust ?? '-'),
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

        .kpi-empty {
            color: #666;
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
                <div>ACHIEVEMENT FOR QUARTER 1 UNTIL 31 MARCH 2026</div>
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
        </div>
    </div>
</x-filament-panels::page>

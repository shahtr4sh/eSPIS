<x-filament-panels::page>
    <style>
        .report-page {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .report-cards {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
        }

        .report-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .report-card-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-bottom: 0.4rem;
        }

        .report-card-value {
            font-size: 1.9rem;
            font-weight: 700;
            color: #111827;
            line-height: 1;
        }

        .report-panel {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .report-panel-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .report-panel-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .report-panel-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .report-table-wrap {
            overflow-x: auto;
        }

        .report-table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        .report-table thead th {
            background: #f9fafb;
            color: #374151;
            font-size: 0.84rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            white-space: nowrap;
        }

        .report-table tbody td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.94rem;
            color: #111827;
            vertical-align: top;
        }

        .report-table tbody tr:hover {
            background: #fcfcfd;
        }

        .report-code {
            font-weight: 700;
            white-space: nowrap;
        }

        .report-type {
            white-space: nowrap;
            font-weight: 600;
        }

        .report-title {
            min-width: 320px;
            max-width: 420px;
            line-height: 1.45;
            word-break: break-word;
        }

        .report-center {
            text-align: center;
            white-space: nowrap;
        }

        .report-right {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 110px;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-achieved {
            background: #dcfce7;
            color: #166534;
        }

        .status-underperforming {
            background: #fef3c7;
            color: #92400e;
        }

        .status-incomplete {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-no-target {
            background: #e5e7eb;
            color: #374151;
        }

        .empty-state {
            padding: 2rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.95rem;
        }

        @media (max-width: 1024px) {
            .report-cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .report-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="report-page">
        {{ $this->form }}

        <div class="report-cards">
            <div class="report-card">
                <div class="report-card-label">Total KPI/PI</div>
                <div class="report-card-value">{{ $summary['total'] ?? 0 }}</div>
            </div>

            <div class="report-card">
                <div class="report-card-label">KPI</div>
                <div class="report-card-value">{{ $summary['kpi'] ?? 0 }}</div>
            </div>

            <div class="report-card">
                <div class="report-card-label">PI</div>
                <div class="report-card-value">{{ $summary['pi'] ?? 0 }}</div>
            </div>

            <div class="report-card">
                <div class="report-card-label">Incomplete</div>
                <div class="report-card-value">{{ $summary['incomplete'] ?? 0 }}</div>
            </div>
        </div>

        <div class="report-panel">
            <div class="report-panel-header">
                <div>
                    <div class="report-panel-title">KPI / PI Report Table</div>
                    <div class="report-panel-subtitle">
                        Quarter: {{ $quarter ?? $this->quarter ?? '-' }} |
                        Year: {{ $year ?? $this->year ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="report-table-wrap">
                <table class="report-table">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Objective</th>
                        <th>Dimension</th>
                        <th>Office</th>
                        <th>Target</th>
                        <th>Achievement</th>
                        <th>%</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        @php
                            $statusClass = match ($row['status'] ?? '') {
                                'Achieved' => 'status-achieved',
                                'Underperforming' => 'status-underperforming',
                                'No Target' => 'status-no-target',
                                default => 'status-incomplete',
                            };
                        @endphp

                        <tr>
                            <td class="report-code">{{ $row['code'] ?? '-' }}</td>
                            <td class="report-type">{{ $row['type'] ?? '-' }}</td>
                            <td class="report-title">{{ $row['prime_objective'] ?? '-' }}</td>
                            <td class="report-center">{{ $row['dimension'] ?? '-' }}</td>
                            <td>{{ $row['office'] ?? '-' }}</td>
                            <td class="report-right">
                                {{ is_null($row['target'] ?? null) ? '-' : number_format((float) $row['target'], 2) }}
                            </td>
                            <td class="report-right">
                                {{ is_null($row['achievement'] ?? null) ? '-' : number_format((float) $row['achievement'], 2) }}
                            </td>
                            <td class="report-right">
                                {{ is_null($row['percent'] ?? null) ? '-' : number_format((float) $row['percent'], 2) . '%' }}
                            </td>
                            <td>
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $row['status'] ?? '-' }}
                                    </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="empty-state">
                                No report data found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>

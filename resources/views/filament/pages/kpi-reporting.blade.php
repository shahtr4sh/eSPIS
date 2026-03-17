<x-filament-panels::page>
    @php
        $selectedQuarter = $quarter ?? $this->quarter ?? 'Q1';
    @endphp
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

        .percent-label {
            width: 10%;
            text-transform: uppercase;
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
            table-layout: fixed; /* Add this for fixed column widths */
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
            white-space: normal; /* Allow headers to wrap */
            word-wrap: break-word;
        }

        .report-table tbody td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.94rem;
            color: #111827;
            vertical-align: top;
            white-space: normal; /* Allow content to wrap */
            word-wrap: break-word;
        }

        .report-table tbody tr:hover {
            background: #fcfcfd;
        }

        .report-code {
            font-weight: 700;
            white-space: nowrap; /* Keep code from wrapping */
        }

        .report-center {
            text-align: center;
            white-space: nowrap; /* Keep numbers from wrapping */
        }

        .report-right {
            text-align: right;
            white-space: nowrap; /* Keep numbers from wrapping */
            font-variant-numeric: tabular-nums;
        }

        .report-indicator {
            max-width: 300px;
            white-space: normal;
            word-wrap: break-word;
        }

        .report-number-cell {
            white-space: normal !important; /* Override to allow wrapping */
            word-wrap: break-word;
            text-align: center;
            font-variant-numeric: tabular-nums;
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
                <div class="report-card-label">Missing Annual Target</div>
                <div class="report-card-value">{{ $summary['missing_annual_target'] ?? 0 }}</div>
            </div>

            <div class="report-card">
                <div class="report-card-label">Missing {{ $selectedQuarter }} Target</div>
                <div class="report-card-value">{{ $summary['missing_quarter_target'] ?? 0 }}</div>
            </div>

            <div class="report-card">
                <div class="report-card-label">Missing {{ $selectedQuarter }} Achievement</div>
                <div class="report-card-value">{{ $summary['missing_quarter_achievement'] ?? 0 }}</div>
            </div>
        </div>

        <div class="report-panel">
            <div class="report-panel-header">
                <div>
                    <div class="report-panel-title">KPI / PI Report Table</div>
                    <div class="report-panel-subtitle">
                        Quarter: {{ $selectedQuarter }} |
                        Year: {{ $year ?? $this->year ?? '-' }}
                    </div>
                </div>
            </div>

            <div class="report-table-wrap">
                <table class="report-table">
                    <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 9%;">Code</th>
                        <th style="width: 30%;">Indicator</th>
                        <th style="width: 8%;">Annual Target</th>
                        <th style="width: 10%;">% Annual Completed</th>
                        <th style="width: 8%;">{{ $selectedQuarter }} Target</th>
                        <th style="width: 8%;">{{ $selectedQuarter }} Achievement</th>
                        <th style="width: 9%;">Evidence</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($rows as $row)
                        <tr>
                            <td class="report-center">{{ $row['no'] ?? '-' }}</td>
                            <td class="report-code">
                                @if(!empty($row['id']))
                                    <a href="{{ url('admin/kpi-pis/' . $row['id']) }}"
                                       class="report-code-link"
                                       style="color: #0000cc; text-decoration: underline;"
                                       target="_blank">
                                        {{ $row['code'] ?? '-' }}
                                    </a>
                                @else
                                    {{ $row['code'] ?? '-' }}
                                @endif
                            </td>
                            <td class="report-indicator">{{ $row['indicator'] ?? '-' }}</td>
                            <td class="report-number-cell">
                                {{ is_null($row['annual_target'] ?? null) ? '-' : number_format((float) $row['annual_target'], 2) }}
                            </td>
                            <td class="report-number-cell">
                                {{ is_null($row['annual_completed_percent'] ?? null) ? '-' : number_format((float) $row['annual_completed_percent'], 2) . '%' }}
                            </td>
                            <td class="report-number-cell">
                                {{ is_null($row['quarter_target'] ?? null) ? '-' : number_format((float) $row['quarter_target'], 2) }}
                            </td>
                            <td class="report-number-cell">
                                {{ is_null($row['quarter_achievement'] ?? null) ? '-' : number_format((float) $row['quarter_achievement'], 2) }}
                            </td>
                            <td>
                                @if(!empty($row['evidence']))
                                    <a href="{{ $row['evidence'] }}" class="text-primary-600 underline" target="_blank">
                                        View
                                    </a>
                                @else
                                    -
                                @endif
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

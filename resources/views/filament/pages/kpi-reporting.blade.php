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
            gap: 1rem;
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
            table-layout: fixed;
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
            white-space: normal;
            word-wrap: break-word;
        }

        .report-table tbody td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.94rem;
            color: #111827;
            vertical-align: top;
            white-space: normal;
            word-wrap: break-word;
        }

        .report-table tbody tr:hover {
            background: #fcfcfd;
        }

        .report-code {
            font-weight: 700;
            white-space: nowrap;
        }

        .report-center {
            text-align: center;
            white-space: nowrap;
        }

        .report-indicator {
            max-width: 300px;
            white-space: normal;
            word-wrap: break-word;
        }

        .report-number-cell {
            white-space: normal !important;
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

        .report-collapse-btn {
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #111827;
            border-radius: 10px;
            padding: 0.55rem 0.9rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .report-collapse-btn:hover {
            background: #f3f4f6;
        }

        .report-collapsible {
            display: none;
        }

        .report-collapsible.is-open {
            display: block;
        }

        .report-chevron {
            display: inline-block;
            margin-left: 0.45rem;
            transition: transform 0.2s ease;
        }

        .report-collapse-btn[aria-expanded="true"] .report-chevron {
            transform: rotate(180deg);
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

            .report-panel-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    <script>
        function toggleReportSection(sectionId, button) {
            const section = document.getElementById(sectionId);

            if (!section) return;

            const isOpen = section.classList.contains('is-open');

            if (isOpen) {
                section.classList.remove('is-open');
                button.setAttribute('aria-expanded', 'false');
                button.querySelector('.report-btn-text').textContent = 'Show Table';
            } else {
                section.classList.add('is-open');
                button.setAttribute('aria-expanded', 'true');
                button.querySelector('.report-btn-text').textContent = 'Hide Table';
            }
        }
    </script>

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

        {{-- SUMMARY REPORT PANEL --}}
        <div class="report-panel">
            <div class="report-panel-header">
                <div>
                    <div class="report-panel-title">KPI / PI Summary Report</div>
                    <div class="report-panel-subtitle">
                        Quarter: {{ $selectedQuarter }} |
                        Year: {{ $year ?? $this->year ?? '-' }}
                    </div>
                </div>

                <button
                    type="button"
                    class="report-collapse-btn"
                    aria-expanded="false"
                    onclick="toggleReportSection('summary-report-section', this)"
                >
                    <span class="report-btn-text">Show Table</span>
                    <span class="report-chevron">▼</span>
                </button>
            </div>

            <div id="summary-report-section" class="report-collapsible">
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
                                <td class="report-center">
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
                                <td colspan="8" class="empty-state">
                                    No summary report data found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- DETAIL REPORT PANEL --}}
        <div class="report-panel">
            <div class="report-panel-header">
                <div>
                    <div class="report-panel-title">Quarter Detail by Unit</div>
                    <div class="report-panel-subtitle">
                        Quarter: {{ $selectedQuarter }} |
                        Year: {{ $year ?? $this->year ?? '-' }}
                    </div>
                </div>

                <button
                    type="button"
                    class="report-collapse-btn"
                    aria-expanded="false"
                    onclick="toggleReportSection('detail-report-section', this)"
                >
                    <span class="report-btn-text">Show Table</span>
                    <span class="report-chevron">▼</span>
                </button>
            </div>

            <div id="detail-report-section" class="report-collapsible">
                <div class="report-table-wrap">
                    <table class="report-table">
                        <thead>
                        <tr>
                            <th style="width: 10%;">Code</th>
                            <th style="width: 28%;">Indicator</th>
                            <th style="width: 12%;">Unit</th>
                            <th style="width: 10%;">Quarter</th>
                            <th style="width: 12%;">Unit Target</th>
                            <th style="width: 12%;">Unit Achievement</th>
                            <th style="width: 12%;">% Completed</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($detail_rows as $row)
                            <tr>
                                <td class="report-code">{{ $row['code'] ?? '-' }}</td>
                                <td class="report-indicator">{{ $row['indicator'] ?? '-' }}</td>
                                <td class="report-center">{{ $row['unit'] ?? '-' }}</td>
                                <td class="report-center">{{ $row['quarter'] ?? '-' }}</td>
                                <td class="report-number-cell">
                                    {{ is_null($row['unit_target'] ?? null) ? '-' : number_format((float) $row['unit_target'], 2) }}
                                </td>
                                <td class="report-number-cell">
                                    {{ is_null($row['unit_achievement'] ?? null) ? '-' : number_format((float) $row['unit_achievement'], 2) }}
                                </td>
                                <td class="report-number-cell">
                                    {{ is_null($row['completion_percent'] ?? null) ? '-' : number_format((float) $row['completion_percent'], 2) . '%' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    No detail report data found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

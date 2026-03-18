<div class="unit-achievement-grid">
    <style>
        .unit-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 16px;
        }

        .unit-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
        }

        .unit-card-header {
            font-weight: 600;
            color: #374151;
            padding-bottom: 8px;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
        }

        .unit-card-body {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .achievement-field {
            width: 100%;
        }

        .achievement-field label {
            display: block;
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 4px;
        }
    </style>

    <div class="mb-4">
        <h3 class="text-lg font-medium">Editing Quarter: {{ $quarter }}</h3>
    </div>

    <div class="unit-grid">
        @foreach($units as $unit)
            <div class="unit-card">
                <div class="unit-card-header">
                    {{ $unit->code }}
                    @if($unit->name)
                        <div style="font-size: 0.75rem; font-weight: normal; color: #6b7280;">
                            {{ $unit->name }}
                        </div>
                    @endif
                </div>

                <div class="unit-card-body">
                    @php
                        $detail = $record->distributionDetails
                            ->where('quarter', $quarter)
                            ->where('distribution_unit_id', $unit->id)
                            ->first();

                        $fieldName = "unit_{$unit->id}_{$quarter}_achievement";
                    @endphp

                    <div class="achievement-field">
                        <label>Achievement Value</label>
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="number"
                                step="0.01"
                                name="{{ $fieldName }}"
                                value="{{ $detail->achievement_value ?? '' }}"
                                placeholder="Enter value"
                                wire:model.defer="data.{{ $fieldName }}"
                            />
                        </x-filament::input.wrapper>
                    </div>

                    <input type="hidden"
                           name="achievements[{{ $unit->id }}][{{ $quarter }}][unit_id]"
                           value="{{ $unit->id }}">

                    <input type="hidden"
                           name="achievements[{{ $unit->id }}][{{ $quarter }}][quarter]"
                           value="{{ $quarter }}">
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 flex justify-end">
        <x-filament::button
            type="button"
            wire:click="saveQuarterAchievements('{{ $quarter }}')"
            color="success"
            size="sm">
            Save {{ $quarter }} Achievements
        </x-filament::button>
    </div>
</div>

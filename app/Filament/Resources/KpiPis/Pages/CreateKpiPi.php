<?php

namespace App\Filament\Resources\KpiPis\Pages;

use App\Filament\Resources\KpiPis\KpiPiResource;
use App\Models\KpiPi;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateKpiPi extends CreateRecord
{
    protected static string $resource = KpiPiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function syncSummaryFromDistributionDetails(KpiPi $record): void
    {
        $details = $record->distributionDetails()->get();

        $record->update([
            'sasaran_q1' => $details->where('quarter', 'Q1')->sum('target_value'),
            'sasaran_q2' => $details->where('quarter', 'Q2')->sum('target_value'),
            'sasaran_q3' => $details->where('quarter', 'Q3')->sum('target_value'),
            'sasaran_q4' => $details->where('quarter', 'Q4')->sum('target_value'),

            'pencapaian_q1' => $details->where('quarter', 'Q1')->sum('achievement_value'),
            'pencapaian_q2' => $details->where('quarter', 'Q2')->sum('achievement_value'),
            'pencapaian_q3' => $details->where('quarter', 'Q3')->sum('achievement_value'),
            'pencapaian_q4' => $details->where('quarter', 'Q4')->sum('achievement_value'),

            'sasaran_tahunan' => $details->sum('target_value'),
            'pencapaian_tahunan' => $details->sum('achievement_value'),
        ]);
    }

}

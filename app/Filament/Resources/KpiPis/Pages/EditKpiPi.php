<?php

namespace App\Filament\Resources\KpiPis\Pages;

use App\Filament\Resources\KpiPis\KpiPiResource;
use App\Models\KpiPi;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditKpiPi extends EditRecord
{
    protected static string $resource = KpiPiResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function syncSummaryFromDistributionDetails(KpiPi $record): void
    {
        $details = $record->distributionDetails()->get();

        $record->update([
            'sasaran_tahunan' => $details->sum('target_value'),
            'pencapaian_tahunan' => $details->sum('achievement_value'),
        ]);
    }

}

<?php

namespace App\Filament\Resources\KpiPis\Pages;

use App\Filament\Resources\KpiPis\KpiPiResource;
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
}

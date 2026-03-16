<?php

namespace App\Filament\Resources\KpiPis\Pages;

use App\Filament\Resources\KpiPis\KpiPiResource;
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
}

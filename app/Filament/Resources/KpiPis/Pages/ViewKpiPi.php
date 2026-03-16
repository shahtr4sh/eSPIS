<?php

namespace App\Filament\Resources\KpiPis\Pages;

use App\Filament\Resources\KpiPis\KpiPiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKpiPi extends ViewRecord
{
    protected static string $resource = KpiPiResource::class;

    protected string $view = 'filament.resources.kpi-pis.pages.view-kpi-pi';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

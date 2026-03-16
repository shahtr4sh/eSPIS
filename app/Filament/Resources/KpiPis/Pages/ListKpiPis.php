<?php

namespace App\Filament\Resources\KpiPis\Pages;

use App\Filament\Resources\KpiPis\KpiPiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKpiPis extends ListRecords
{
    protected static string $resource = KpiPiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

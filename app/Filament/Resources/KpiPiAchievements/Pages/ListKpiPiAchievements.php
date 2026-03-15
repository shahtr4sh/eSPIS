<?php

namespace App\Filament\Resources\KpiPiAchievements\Pages;

use App\Filament\Resources\KpiPiAchievements\KpiPiAchievementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKpiPiAchievements extends ListRecords
{
    protected static string $resource = KpiPiAchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

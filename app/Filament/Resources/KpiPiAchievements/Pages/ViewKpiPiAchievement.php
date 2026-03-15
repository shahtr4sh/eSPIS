<?php

namespace App\Filament\Resources\KpiPiAchievements\Pages;

use App\Filament\Resources\KpiPiAchievements\KpiPiAchievementResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewKpiPiAchievement extends ViewRecord
{
    protected static string $resource = KpiPiAchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\KpiPiAchievements\Pages;

use App\Filament\Resources\KpiPiAchievements\KpiPiAchievementResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKpiPiAchievement extends EditRecord
{
    protected static string $resource = KpiPiAchievementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\NutritionPlans\Pages;

use App\Filament\Resources\NutritionPlans\NutritionPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNutritionPlans extends ListRecords
{
    protected static string $resource = NutritionPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

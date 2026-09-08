<?php

namespace App\Filament\Resources\ExercisePrograms\Pages;

use App\Filament\Resources\ExercisePrograms\ExerciseProgramResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExercisePrograms extends ListRecords
{
    protected static string $resource = ExerciseProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

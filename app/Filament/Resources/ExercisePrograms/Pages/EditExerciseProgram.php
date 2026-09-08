<?php

namespace App\Filament\Resources\ExercisePrograms\Pages;

use App\Filament\Resources\ExercisePrograms\ExerciseProgramResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExerciseProgram extends EditRecord
{
    protected static string $resource = ExerciseProgramResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

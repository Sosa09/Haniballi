<?php

namespace App\Filament\Resources\ExercisePrograms;

use App\Filament\Resources\ExercisePrograms\Pages\CreateExerciseProgram;
use App\Filament\Resources\ExercisePrograms\Pages\EditExerciseProgram;
use App\Filament\Resources\ExercisePrograms\Pages\ListExercisePrograms;
use App\Models\ExerciseProgram;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExerciseProgramResource extends Resource
{
    protected static ?string $model = ExerciseProgram::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static string|\UnitEnum|null $navigationGroup = 'Care Plans';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Program Information')
                    ->schema([
                        Select::make('patient_id')
                            ->relationship('patient', 'first_name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->label('Created By (Doctor)')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            Select::make('category')
                                ->required()
                                ->options([
                                    'stretching' => 'Stretching',
                                    'strength' => 'Strength',
                                    'cardio' => 'Cardio',
                                    'rehabilitation' => 'Rehabilitation',
                                    'yoga' => 'Yoga',
                                ])
                                ->native(false),
                            Select::make('difficulty')
                                ->required()
                                ->options([
                                    'beginner' => 'Beginner',
                                    'intermediate' => 'Intermediate',
                                    'advanced' => 'Advanced',
                                ])
                                ->native(false),
                            TextInput::make('duration_minutes')
                                ->numeric()
                                ->suffix('min')
                                ->minValue(5)
                                ->maxValue(300),
                            TextInput::make('sessions_per_week')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(14),
                            Select::make('status')
                                ->required()
                                ->options([
                                    'active' => 'Active',
                                    'completed' => 'Completed',
                                    'draft' => 'Draft',
                                    'archived' => 'Archived',
                                ])
                                ->default('active')
                                ->native(false),
                            TextInput::make('video_url')
                                ->url()
                                ->label('Video URL')
                                ->maxLength(500),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.first_name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(40),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'stretching' => 'info',
                        'strength' => 'warning',
                        'cardio' => 'danger',
                        'rehabilitation' => 'success',
                        'yoga' => 'pink',
                        default => 'gray',
                    }),
                TextColumn::make('difficulty')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'beginner' => 'success',
                        'intermediate' => 'warning',
                        'advanced' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sessions_per_week')
                    ->label('Sessions/Week')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'info',
                        'draft' => 'warning',
                        'archived' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options([
                        'stretching' => 'Stretching',
                        'strength' => 'Strength',
                        'cardio' => 'Cardio',
                        'rehabilitation' => 'Rehabilitation',
                        'yoga' => 'Yoga',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'completed' => 'Completed',
                        'draft' => 'Draft',
                        'archived' => 'Archived',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExercisePrograms::route('/'),
            'create' => CreateExerciseProgram::route('/create'),
            'edit' => EditExerciseProgram::route('/{record}/edit'),
        ];
    }
}

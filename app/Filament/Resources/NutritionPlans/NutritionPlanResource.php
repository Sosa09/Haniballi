<?php

namespace App\Filament\Resources\NutritionPlans;

use App\Filament\Resources\NutritionPlans\Pages\CreateNutritionPlan;
use App\Filament\Resources\NutritionPlans\Pages\EditNutritionPlan;
use App\Filament\Resources\NutritionPlans\Pages\ListNutritionPlans;
use App\Models\NutritionPlan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

class NutritionPlanResource extends Resource
{
    protected static ?string $model = NutritionPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Care Plans';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plan Information')
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
                            DatePicker::make('start_date')
                                ->native(false)
                                ->required(),
                            DatePicker::make('end_date')
                                ->native(false),
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
                        ]),
                    ]),
                Section::make('Nutritional Targets')
                    ->schema([
                        Grid::make(4)->schema([
                            TextInput::make('daily_calories')
                                ->numeric()
                                ->suffix('kcal')
                                ->minValue(0),
                            TextInput::make('protein_grams')
                                ->numeric()
                                ->suffix('g')
                                ->minValue(0),
                            TextInput::make('carbs_grams')
                                ->numeric()
                                ->suffix('g')
                                ->minValue(0),
                            TextInput::make('fat_grams')
                                ->numeric()
                                ->suffix('g')
                                ->minValue(0),
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
                TextColumn::make('daily_calories')
                    ->label('Calories')
                    ->suffix(' kcal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
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
            'index' => ListNutritionPlans::route('/'),
            'create' => CreateNutritionPlan::route('/create'),
            'edit' => EditNutritionPlan::route('/{record}/edit'),
        ];
    }
}

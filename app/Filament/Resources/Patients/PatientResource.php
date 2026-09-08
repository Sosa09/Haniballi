<?php

namespace App\Filament\Resources\Patients;

use App\Filament\Resources\Patients\Pages\CreatePatient;
use App\Filament\Resources\Patients\Pages\EditPatient;
use App\Filament\Resources\Patients\Pages\ListPatients;
use App\Models\Patient;
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

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Patients';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('user_id')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->label('Linked User Account')
                                ->columnSpanFull(),
                            TextInput::make('first_name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('last_name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->tel()
                                ->maxLength(50),
                            DatePicker::make('date_of_birth')
                                ->native(false),
                            Select::make('gender')
                                ->options([
                                    'male' => 'Male',
                                    'female' => 'Female',
                                    'other' => 'Other',
                                ])
                                ->native(false),
                            Select::make('status')
                                ->required()
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    'pending' => 'Pending',
                                ])
                                ->default('active')
                                ->native(false),
                        ]),
                    ]),
                Section::make('Physical Measurements')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('height_cm')
                                ->numeric()
                                ->suffix('cm')
                                ->minValue(0)
                                ->maxValue(300),
                            TextInput::make('weight_kg')
                                ->numeric()
                                ->suffix('kg')
                                ->minValue(0)
                                ->maxValue(500),
                        ]),
                    ]),
                Section::make('Medical Information')
                    ->schema([
                        Textarea::make('medical_notes')
                            ->rows(4)
                            ->columnSpanFull(),
                        Textarea::make('allergies')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name']),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('gender')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'male' => 'info',
                        'female' => 'pink',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'pending' => 'warning',
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
                        'inactive' => 'Inactive',
                        'pending' => 'Pending',
                    ]),
                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                        'other' => 'Other',
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
            'index' => ListPatients::route('/'),
            'create' => CreatePatient::route('/create'),
            'edit' => EditPatient::route('/{record}/edit'),
        ];
    }
}

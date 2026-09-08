<?php

namespace App\Filament\Resources\Appointments;

use App\Filament\Resources\Appointments\Pages\CreateAppointment;
use App\Filament\Resources\Appointments\Pages\EditAppointment;
use App\Filament\Resources\Appointments\Pages\ListAppointments;
use App\Models\Appointment;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|\UnitEnum|null $navigationGroup = 'Scheduling';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Appointment Details')
                    ->schema([
                        Grid::make(2)->schema([
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
                                ->label('Doctor')
                                ->required()
                                ->columnSpanFull(),
                            DateTimePicker::make('scheduled_at')
                                ->required()
                                ->native(false),
                            TextInput::make('duration_minutes')
                                ->required()
                                ->numeric()
                                ->default(30)
                                ->suffix('min')
                                ->minValue(15)
                                ->maxValue(180),
                            Select::make('type')
                                ->required()
                                ->options([
                                    'video' => 'Video Consultation',
                                    'in_person' => 'In Person',
                                    'phone' => 'Phone Call',
                                ])
                                ->default('video')
                                ->native(false),
                            Select::make('status')
                                ->required()
                                ->options([
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->default('pending')
                                ->native(false),
                        ]),
                    ]),
                Section::make('Notes')
                    ->schema([
                        Textarea::make('notes')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),
                Section::make('Cancellation')
                    ->schema([
                        Grid::make(2)->schema([
                            DateTimePicker::make('cancelled_at')
                                ->native(false),
                            Textarea::make('cancellation_reason')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                    ])
                    ->collapsed(),
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
                TextColumn::make('user.name')
                    ->label('Doctor')
                    ->searchable(),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'video' => 'info',
                        'in_person' => 'success',
                        'phone' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'completed' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('duration_minutes')
                    ->label('Duration')
                    ->suffix(' min')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        'video' => 'Video',
                        'in_person' => 'In Person',
                        'phone' => 'Phone Call',
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
            'index' => ListAppointments::route('/'),
            'create' => CreateAppointment::route('/create'),
            'edit' => EditAppointment::route('/{record}/edit'),
        ];
    }
}

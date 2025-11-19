<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Gestión Clínica';

    protected static ?string $navigationLabel = 'Citas';
    
    protected static ?string $modelLabel = 'Cita';
    
    protected static ?string $pluralModelLabel = 'Citas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Cita')
                    ->schema([
                        // Tarea II.2: Formulario para creación manual
                        Forms\Components\Select::make('patient_id')
                            ->label('Paciente')
                            ->options(Patient::all()->pluck('name', 'id')) // Carga todos los pacientes
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\Select::make('doctor_id')
                            ->label('Doctor')
                            ->options(Doctor::all()->pluck('name', 'id')) // Carga todos los doctores
                            ->searchable()
                            ->required(),
                        
                        Forms\Components\DatePicker::make('date')
                            ->label('Fecha')
                            ->required()
                            ->native(false), // Usar el datepicker de Filament
                        
                        Forms\Components\TimePicker::make('time')
                            ->label('Hora')
                            ->required()
                            ->seconds(false) // Ocultar segundos
                            ->minutesStep(30), // Citas cada 30 min
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'confirmado' => 'Confirmado',
                                'atendido' => 'Atendido',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required()
                            ->default('confirmado'), // Si la secretaria lo crea, se confirma
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas (Motivo de la cita)')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.name')
                    ->label('Paciente')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('date')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('time')
                    ->label('Hora')
                    ->time('H:i'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge() // <-- Muestra el estado como una "píldora"
                    ->color(fn (string $state): string => match ($state) {
                        'pendiente' => 'warning',
                        'confirmado' => 'primary',
                        'atendido' => 'success',
                        'cancelado' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
            ])
            ->filters([
                // Filtro para ver citas por estado
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'confirmado' => 'Confirmado',
                        'atendido' => 'Atendido',
                        'cancelado' => 'Cancelado',
                    ]),
                // Filtro para ver citas por doctor
                Tables\Filters\SelectFilter::make('doctor_id')
                    ->label('Doctor')
                    ->options(Doctor::all()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
            'create' => Pages\CreateAppointment::route('/create'),
            'edit' => Pages\EditAppointment::route('/{record}/edit'),
        ];
    }

    // --- Permisos de Spatie (Corregidos con Auth::user()) ---
    // (Asegúrate de crear estos permisos y asignarlos al rol 'admin'/'secretary')

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->can('create_appointments');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->can('edit_appointments');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->can('delete_appointments');
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->can('view_appointments');
    }
}
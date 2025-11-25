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
use App\Models\User;

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
                        
                        Forms\Components\Select::make('time')
                            ->label('Hora')
                            ->required()
                            ->options(function (Forms\Get $get) {
                                $doctorId = $get('doctor_id');
                                $date = $get('date');

                                if (!$doctorId || !$date) {
                                    return [];
                                }

                                // Llamada interna al controlador (o lógica duplicada para rapidez)
                                // Para hacerlo limpio en Filament, duplicaremos la lógica de generación aquí
                                // o haríamos una llamada HTTP a la propia API (menos eficiente).
                                // Duplicamos lógica por simplicidad y performance:
                                
                                $doctor = Doctor::find($doctorId);
                                if (!$doctor) return [];

                                $slots = [];
                                $interval = 30;

                                $periods = [
                                    ['start' => $doctor->morning_start, 'end' => $doctor->morning_end],
                                    ['start' => $doctor->afternoon_start, 'end' => $doctor->afternoon_end],
                                ];

                                foreach ($periods as $period) {
                                    // Asegurar que $date sea solo Y-m-d
                                    $dateOnly = \Carbon\Carbon::parse($date)->format('Y-m-d');
                                    $start = \Carbon\Carbon::parse($dateOnly . ' ' . $period['start']);
                                    $end = \Carbon\Carbon::parse($dateOnly . ' ' . $period['end']);

                                    while ($start < $end) {
                                        $timeStr = $start->format('H:i');
                                        $slots[$timeStr] = $timeStr; // Key => Label
                                        $start->addMinutes($interval);
                                    }
                                }

                                // Excluir ocupados
                                $bookedTimes = Appointment::where('doctor_id', $doctorId)
                                    ->whereDate('date', $date)
                                    ->where('status', '!=', 'cancelado')
                                    ->pluck('time')
                                    ->map(fn($t) => \Carbon\Carbon::parse($t)->format('H:i'))
                                    ->toArray();

                                return array_diff($slots, $bookedTimes);
                            })
                            ->searchable()
                            ->preload(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'confirmado' => 'Confirmado',
                                'atendido' => 'Atendido',
                                'cancelado' => 'Cancelado',
                            ])
                            ->required()
                            ->default('confirmado'),
                        
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

    // public static function canCreate(): bool
    // {
    //     /** @var \App\Models\User $user */
    //     $user = Auth::user();
    //     return $user->can('create_appointments');
    // }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
    $user = Auth::user();

    // Verificamos que exista el usuario y tenga el permiso
    return $user && $user->can('create_appointments');



        // // Solo permite si el usuario tiene el permiso 'create_appointments'
        // return auth()->user()->can('create_appointments');
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

    /** @var \App\Models\User $user */
    $user = Auth::user();

    // Primero validamos que haya un usuario logueado para no romper nada
    if (!$user) {
        return $query;
    }
        if ($user->hasRole('doctor')) {
            // El usuario es doctor, filtrar por su registro de doctor asociado
            $doctor = $user->doctor;
            if ($doctor) {
                $query->where('doctor_id', $doctor->id);
            } else {
                // Si es usuario doctor pero no tiene registro de doctor, no ver nada (seguridad)
                $query->whereRaw('1 = 0'); 
            }
        }

        return $query;
    }
    
}
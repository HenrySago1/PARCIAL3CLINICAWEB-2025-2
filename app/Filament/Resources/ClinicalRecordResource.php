<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClinicalRecordResource\Pages;
use App\Filament\Resources\ClinicalRecordResource\RelationManagers;
use App\Models\ClinicalRecord;
use App\Models\Patient;
use App\Models\Appointment; // <-- Importante
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get; // <-- Importante para el formulario dinámico
use Illuminate\Support\Collection; // <-- Importante para el formulario dinámico

class ClinicalRecordResource extends Resource
{
    protected static ?string $model = ClinicalRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Gestión Clínica';

    protected static ?string $navigationLabel = 'Historial Clínico';

    protected static ?string $modelLabel = 'Historial';

    protected static ?string $pluralModelLabel = 'Historiales';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Paciente y Cita')
                    ->schema([
                        Forms\Components\Select::make('patient_id')
                            ->label('Paciente')
                            ->relationship('patient', 'name')
                            ->searchable()
                            ->required()
                            ->live() // <-- Hace el formulario dinámico
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('appointment_id', null)), // Resetea la cita si cambia el paciente

                        Forms\Components\Select::make('appointment_id')
                            ->label('Cita Asociada (Pendiente)')
                            ->required()
                            ->options(function (Get $get): Collection {
                                $patientId = $get('patient_id');
                                if (!$patientId) {
                                    return collect();
                                }

                                // Obtenemos las citas y formateamos la etiqueta manualmente
                                return Appointment::where('patient_id', $patientId)
                                    ->where('status', 'pendiente')
                                    ->get()
                                    ->mapWithKeys(function ($appointment) {
                                        // Creamos un string legible: "Fecha Hora"
                                        return [$appointment->id => "{$appointment->date} {$appointment->time}"];
                                    });
                            })
                            ->searchable(),

                        // Asigna al doctor logueado automáticamente
                        Forms\Components\Hidden::make('doctor_id')
                            ->default(fn() => Auth::user()->doctor->id ?? null)
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Diagnóstico y Tratamiento')
                    ->schema([
                        Forms\Components\RichEditor::make('diagnosis')
                            ->label('Diagnóstico')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('treatment')
                            ->label('Tratamiento')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notas Adicionales')
                            ->columnSpanFull(),
                    ]),
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculto si es un doctor
                Tables\Columns\TextColumn::make('diagnosis')
                    ->label('Diagnóstico (Resumen)')
                    ->html() // Para que respete el formato
                    ->limit(50), // Acortado
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Atención')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // --- TAREA II.5: SEGURIDAD DEL DOCTOR ---
    // Solo muestra los historiales del doctor que está logueado
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var \App\Models\User $user */ // <--- ¡ESTA LÍNEA ARREGLA EL ERROR ROJO!
        $user = Auth::user();

        if ($user->isDoctor()) {
            // Si es doctor, filtra por su ID de doctor
            return $query->where('doctor_id', $user->doctor->id);
        }

        // Si es admin o secretaria, muestra todo
        return $query;
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
            'index' => Pages\ListClinicalRecords::route('/'),
            'create' => Pages\CreateClinicalRecord::route('/create'),
            'edit' => Pages\EditClinicalRecord::route('/{record}/edit'),
            'view' => Pages\ViewClinicalRecord::route('/{record}'),
        ];
    }

    // --- Permisos (Corregidos con Auth::user() y @var) ---

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Solo doctores pueden crear historiales
        return $user->can('create_clinical_records');
    }

    // (Añade el resto de permisos si los necesitas)
}

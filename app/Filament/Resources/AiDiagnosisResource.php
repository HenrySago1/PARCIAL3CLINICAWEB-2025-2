<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiDiagnosisResource\Pages;
use App\Models\AiDiagnosis;
use App\Services\AiDiagnosisService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;

class AiDiagnosisResource extends Resource
{
    protected static ?string $model = AiDiagnosis::class;
    protected static ?string $navigationIcon = 'heroicon-o-eye';
    protected static ?string $navigationLabel = 'Diagnóstico IA';
    protected static ?string $modelLabel = 'Diagnóstico';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- SECCIÓN 1: DATOS DEL PACIENTE ---
                Forms\Components\Section::make('Información del Paciente')
                    ->schema([
                        Forms\Components\Select::make('patient_id')
                            ->label('Paciente')
                            ->relationship('patient', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Hidden::make('doctor_id')
                            ->default(fn() => Auth::user()->doctor->id ?? null),

                        Forms\Components\Hidden::make('status')
                            ->default('pendiente'),
                    ]),

                // --- SECCIÓN 2: ANÁLISIS OFTALMOLÓGICO ---
                Forms\Components\Section::make('Análisis e Imágenes')
                    ->description('Sube la imagen y presiona el botón "Analizar" para obtener el resultado.')
                    ->schema([

                        // --- COLUMNA OJO DERECHO (OD) ---
                        Forms\Components\Group::make([
                            Forms\Components\FileUpload::make('image_path_right')
                                ->label('Ojo Derecho (OD)')
                                ->image()
                                ->directory('ai-diagnoses/right')
                                ->visibility('public')
                                // BOTÓN DE ACCIÓN OJO DERECHO
                                ->hintAction(
                                    Action::make('analyze_right')
                                        ->label('Analizar OD 🤖')
                                        ->icon('heroicon-o-cpu-chip')
                                        ->color('primary')
                                        ->action(function ($state, Forms\Set $set, AiDiagnosis $record) {
                                            if (!$state) {
                                                Notification::make()->title('Sube una imagen primero')->warning()->send();
                                                return;
                                            }

                                            $imagePath = is_array($state) ? array_values($state)[0] : $state;

                                            Notification::make()->title('Analizando...')->info()->send();

                                            $service = new AiDiagnosisService();
                                            $data = $service->analyzeSingleImage($imagePath);

                                            if (isset($data['error'])) {
                                                Notification::make()->title('Error IA')->body($data['error'])->danger()->send();
                                            } else {
                                                // 1. Visual
                                                $set('result_right', $data['resultado']);
                                                $set('probability_right', $data['probabilidad']); // <-- AQUÍ DICE 'probabilidad'

                                                // 2. Auto-guardado
                                                $record->update([
                                                    'result_right' => $data['resultado'],
                                                    'probability_right' => $data['probabilidad'], // <--- ¡CORREGIDO AQUÍ! (Antes decía probability)
                                                    'status' => 'completado'
                                                ]);

                                                Notification::make()
                                                    ->title('¡Guardado!')
                                                    ->body("Diagnóstico OD: " . strtoupper($data['resultado']))
                                                    ->success()
                                                    ->send();
                                            }
                                        })
                                ),

                            Forms\Components\TextInput::make('result_right')
                                ->label('Diagnóstico OD')
                                ->readOnly(),

                            Forms\Components\TextInput::make('probability_right')
                                ->label('Certeza (%)')
                                ->suffix('%')
                                ->numeric()
                                ->readOnly(),
                        ]),

                        // --- COLUMNA OJO IZQUIERDO (OI) ---
                        Forms\Components\Group::make([
                            Forms\Components\FileUpload::make('image_path_left')
                                ->label('Ojo Izquierdo (OI)')
                                ->image()
                                ->directory('ai-diagnoses/left')
                                ->visibility('public')
                                // BOTÓN DE ACCIÓN OJO IZQUIERDO
                                ->hintAction(
                                    Action::make('analyze_left')
                                        ->label('Analizar OI 🤖')
                                        ->icon('heroicon-o-cpu-chip')
                                        ->color('primary')
                                        ->action(function ($state, Forms\Set $set, AiDiagnosis $record) {
                                            if (!$state) {
                                                Notification::make()->title('Sube una imagen primero')->warning()->send();
                                                return;
                                            }

                                            $imagePath = is_array($state) ? array_values($state)[0] : $state;

                                            Notification::make()->title('Analizando...')->info()->send();

                                            $service = new AiDiagnosisService();
                                            $data = $service->analyzeSingleImage($imagePath);

                                            if (isset($data['error'])) {
                                                Notification::make()->title('Error IA')->body($data['error'])->danger()->send();
                                            } else {
                                                // 1. Visual
                                                $set('result_left', $data['resultado']);
                                                $set('probability_left', $data['probabilidad']);

                                                // 2. Auto-guardado
                                                $record->update([
                                                    'result_left' => $data['resultado'],
                                                    'probability_left' => $data['probabilidad'], // <--- ¡CORREGIDO AQUÍ TAMBIÉN!
                                                    'status' => 'completado'
                                                ]);

                                                Notification::make()
                                                    ->title('¡Guardado!')
                                                    ->body("Diagnóstico OI: " . strtoupper($data['resultado']))
                                                    ->success()
                                                    ->send();
                                            }
                                        })
                                ),

                            Forms\Components\TextInput::make('result_left')
                                ->label('Diagnóstico OI')
                                ->readOnly(),

                            Forms\Components\TextInput::make('probability_left')
                                ->label('Certeza (%)')
                                ->suffix('%')
                                ->numeric()
                                ->readOnly(),
                        ]),

                    ])->columns(2),
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

                Tables\Columns\TextColumn::make('result_right')
                    ->label('OD')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'glaucoma' => 'danger',
                        'cataract' => 'warning',
                        'normal' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('result_left')
                    ->label('OI')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => strtoupper($state))
                    ->color(fn(string $state): string => match (strtolower($state)) {
                        'glaucoma' => 'danger',
                        'cataract' => 'warning',
                        'normal' => 'success',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiDiagnoses::route('/'),
            'create' => Pages\CreateAiDiagnosis::route('/create'),
            'view' => Pages\ViewAiDiagnosis::route('/{record}'),
        ];
    }

    // --- CORRECCIÓN DE PERMISOS (Evita error "Undefined method can") ---

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->can('create_ai_diagnoses');
    }

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->can('view_ai_diagnoses');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->can('delete_ai_diagnoses');
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user && $user->can('view_ai_diagnoses');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return true;
    }
}

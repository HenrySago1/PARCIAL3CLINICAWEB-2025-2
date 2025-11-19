<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    // --- TRADUCCIÓN ---
    protected static ?string $navigationLabel = 'Pacientes';
    protected static ?string $modelLabel = 'Paciente';
    protected static ?string $pluralModelLabel = 'Pacientes';
    protected static ?string $navigationGroup = 'Gestión Clínica';
    // ------------------

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Acceso App Móvil')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->required()->email()->unique(User::class, 'email', ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->password()->required()->visibleOn('create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                    ])->columns(2),

                Forms\Components\Section::make('Datos del Paciente')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nombre')->required(),
                        Forms\Components\TextInput::make('carnet_identidad')->label('CI')->required(),
                        Forms\Components\TextInput::make('phone')->label('Teléfono'),
                        Forms\Components\DatePicker::make('birthdate')->label('Fecha Nacimiento'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('carnet_identidad')->label('CI'),
                Tables\Columns\TextColumn::make('phone')->label('Teléfono'),
            ])
            ->actions([ Tables\Actions\EditAction::make() ]);
    }

    // Hook User
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $user->assignRole('paciente');
        $data['user_id'] = $user->id;
        unset($data['email'], $data['password']);
        return $data;
    }

    public static function getRelations(): array { return []; }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
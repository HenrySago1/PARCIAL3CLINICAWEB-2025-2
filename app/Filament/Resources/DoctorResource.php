<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    
    // --- ¡AQUÍ ESTÁ LA TRADUCCIÓN! ---
    // Si estas líneas no existen, Filament usa "Doctors" por defecto.
    // Al agregarlas, forzamos el español.
    protected static ?string $navigationLabel = 'Médicos'; 
    protected static ?string $modelLabel = 'Médico';
    protected static ?string $pluralModelLabel = 'Médicos';
    protected static ?string $navigationGroup = 'Gestión Clínica';
    // ----------------------------------

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Credenciales')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email Acceso')
                            ->required()->email()->unique(User::class, 'email', ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()->required()->visibleOn('create')
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                    ])->columns(2),
                
                Forms\Components\Section::make('Datos Personales')
                    ->schema([
                        Forms\Components\TextInput::make('name')->label('Nombre Completo')->required(),
                        Forms\Components\TextInput::make('specialty')->label('Especialidad')->default('Oftalmología'),
                        // Agrega aquí tus campos de horario si los tienes
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nombre')->searchable(),
                Tables\Columns\TextColumn::make('user.email')->label('Email'),
                Tables\Columns\TextColumn::make('specialty')->label('Especialidad'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    // Hook para crear usuario
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name' => $data['name'], 
            'email' => $data['email'],
            'password' => $data['password'],
        ]);
        $user->assignRole('doctor');
        $data['user_id'] = $user->id;
        unset($data['email'], $data['password']);
        return $data;
    }

    public static function getRelations(): array { return []; }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }

    // --- PERMISOS (Ajustados para Secretaria) ---

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // Admin y Secretaria pueden VER la lista
        return $user->hasRole(['admin', 'secretary']);
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole(['admin', 'secretary']);
    }

    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // ¡SOLO ADMIN PUEDE CREAR!
        return $user->hasRole('admin');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // ¡SOLO ADMIN PUEDE EDITAR!
        return $user->hasRole('admin');
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        // ¡SOLO ADMIN PUEDE BORRAR!
        return $user->hasRole('admin');
    }
}
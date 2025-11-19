<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        $permissions = [
            // Permisos generales
            'view_dashboard',
            
            // Permisos de pacientes
            'view_patients',
            'create_patients',
            'edit_patients',
            'delete_patients',
            
            // Permisos de doctores
            'view_doctors',
            'create_doctors',
            'edit_doctors',
            'delete_doctors',
            
            // Permisos de citas
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'delete_appointments',
            'manage_appointments',
            
            // Permisos de expedientes clínicos
            'view_clinical_records',
            'create_clinical_records',
            'edit_clinical_records',
            'delete_clinical_records',
            
            // Permisos de diagnósticos AI (solo doctores y admin)
            'view_ai_diagnoses',
            'create_ai_diagnoses',
            'edit_ai_diagnoses',
            'delete_ai_diagnoses',
            'analyze_ai_diagnoses',
            
            // Permisos de pagos
            'view_payments',
            'create_payments',
            'edit_payments',
            'delete_payments',
            
            // Permisos de administración
            'manage_users',
            'manage_roles',
            'view_reports',
            'system_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Crear roles
        $adminRole = Role::create(['name' => 'admin']);
        $doctorRole = Role::create(['name' => 'doctor']);
        $secretaryRole = Role::create(['name' => 'secretary']);

        // Asignar permisos al rol de administrador (todos los permisos)
        $adminRole->givePermissionTo(Permission::all());

        // Asignar permisos al rol de doctor
        $doctorPermissions = [
            'view_dashboard',
            'view_patients',
            'create_patients',
            'edit_patients',
            'view_doctors',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'manage_appointments',
            'view_clinical_records',
            'create_clinical_records',
            'edit_clinical_records',
            'view_ai_diagnoses',
            'create_ai_diagnoses',
            'edit_ai_diagnoses',
            'analyze_ai_diagnoses',
            'view_payments',
            'create_payments',
            'edit_payments',
        ];
        $doctorRole->givePermissionTo($doctorPermissions);

        // Asignar permisos al rol de secretaria
        $secretaryPermissions = [
            'view_dashboard',
            'view_patients',
            'create_patients',
            'edit_patients',
            //'view_doctors',
            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'manage_appointments',
            'view_clinical_records',
            'create_clinical_records',
            'edit_clinical_records',
            'view_payments',
            'create_payments',
            'edit_payments',
        ];
        $secretaryRole->givePermissionTo($secretaryPermissions);

        // Crear usuario administrador por defecto
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@clinica.com',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole('admin');

        // Crear usuario doctor por defecto
        $doctorUser = User::create([
            'name' => 'Dr. Juan Pérez',
            'email' => 'doctor@clinica.com',
            'password' => bcrypt('password'),
        ]);
        $doctorUser->assignRole('doctor');

        // Crear usuario secretaria por defecto
        $secretaryUser = User::create([
            'name' => 'María González',
            'email' => 'secretaria@clinica.com',
            'password' => bcrypt('password'),
        ]);
        $secretaryUser->assignRole('secretary');

        // Vincular el primer doctor existente con el usuario doctor
        $firstDoctor = \App\Models\Doctor::first();
        if ($firstDoctor) {
            $firstDoctor->update(['user_id' => $doctorUser->id]);
        }

        $this->command->info('Roles y permisos creados exitosamente!');
        $this->command->info('Usuarios por defecto:');
        $this->command->info('- Admin: admin@clinica.com / password');
        $this->command->info('- Doctor: doctor@clinica.com / password');
        $this->command->info('- Secretaria: secretaria@clinica.com / password');
    }
}

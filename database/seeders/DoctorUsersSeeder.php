<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;

class DoctorUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vincular el primer doctor con el usuario doctor existente
        $doctorUser = User::where('email', 'doctor@clinica.com')->first();
        $firstDoctor = Doctor::where('email', 'maria.gonzalez@clinica.com')->first();
        if ($doctorUser && $firstDoctor) {
            $firstDoctor->update(['user_id' => $doctorUser->id]);
        }

        // Crear usuario para el segundo doctor
        $secondDoctor = Doctor::where('email', 'carlos.rodriguez@clinica.com')->first();
        if ($secondDoctor) {
            $doctorUser2 = User::create([
                'name' => 'Dr. Carlos Rodríguez',
                'email' => 'carlos@clinica.com',
                'password' => bcrypt('password'),
            ]);
            $doctorUser2->assignRole('doctor');
            $secondDoctor->update(['user_id' => $doctorUser2->id]);
        }

        // Crear usuario para el tercer doctor
        $thirdDoctor = Doctor::where('email', 'ana.martinez@clinica.com')->first();
        if ($thirdDoctor) {
            $doctorUser3 = User::create([
                'name' => 'Dra. Ana Martínez',
                'email' => 'ana@clinica.com',
                'password' => bcrypt('password'),
            ]);
            $doctorUser3->assignRole('doctor');
            $thirdDoctor->update(['user_id' => $doctorUser3->id]);
        }

        $this->command->info('Usuarios de doctores creados exitosamente!');
        $this->command->info('Doctores adicionales:');
        $this->command->info('- Dr. Carlos: carlos@clinica.com / password');
        $this->command->info('- Dra. Ana: ana@clinica.com / password');
    }
}

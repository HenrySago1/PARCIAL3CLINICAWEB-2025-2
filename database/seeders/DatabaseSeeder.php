<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Appointment;
use App\Models\ClinicalRecord;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar el seeder de roles y permisos primero
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Crear doctores
        $doctors = [
            [
                'name' => 'Dr. María González',
                'email' => 'maria.gonzalez@clinica.com',
                'specialty' => 'Oftalmología General',
                'morning_start' => '08:00:00',
                'morning_end' => '12:00:00',
                'afternoon_start' => '14:00:00',
                'afternoon_end' => '18:00:00',
            ],
            [
                'name' => 'Dr. Carlos Rodríguez',
                'email' => 'carlos.rodriguez@clinica.com',
                'specialty' => 'Retina',
                'morning_start' => '09:00:00',
                'morning_end' => '13:00:00',
                'afternoon_start' => '15:00:00',
                'afternoon_end' => '19:00:00',
            ],
            [
                'name' => 'Dra. Ana Martínez',
                'email' => 'ana.martinez@clinica.com',
                'specialty' => 'Glaucoma',
                'morning_start' => '08:30:00',
                'morning_end' => '12:30:00',
                'afternoon_start' => '14:30:00',
                'afternoon_end' => '18:30:00',
            ],
        ];

        foreach ($doctors as $doctorData) {
            Doctor::create($doctorData);
        }

        // Crear pacientes
        $patients = [
            [
                'name' => 'Juan Pérez',
                'email' => 'juan.perez@email.com',
                'phone' => '+52 55 1234 5678',
                'birthdate' => '1985-03-15',
                'address' => 'Av. Reforma 123, Ciudad de México',
                'carnet_identidad' => '1234567',
                'password' => Hash::make('secret123'),
            ],
            [
                'name' => 'María López',
                'email' => 'maria.lopez@email.com',
                'phone' => '+52 55 9876 5432',
                'birthdate' => '1990-07-22',
                'address' => 'Calle Juárez 456, Guadalajara',
                'carnet_identidad' => '2345678',
                'password' => Hash::make('secret123'),
            ],
            [
                'name' => 'Roberto Sánchez',
                'email' => 'roberto.sanchez@email.com',
                'phone' => '+52 55 5555 1234',
                'birthdate' => '1978-11-08',
                'address' => 'Blvd. Constitución 789, Monterrey',
                'carnet_identidad' => '3456789',
                'password' => Hash::make('secret123'),
            ],
            [
                'name' => 'Carmen Torres',
                'email' => 'carmen.torres@email.com',
                'phone' => '+52 55 4444 5678',
                'birthdate' => '1992-05-12',
                'address' => 'Calle Morelos 321, Puebla',
                'carnet_identidad' => '4567890',
                'password' => Hash::make('secret123'),
            ],
        ];

        foreach ($patients as $patientData) {
            Patient::create($patientData);
        }

        // Crear historiales medicos
        $clinicalRecords = [
            [
                'code' => 'EXP-001',
                'patient_id' => 1,
                'observations' => 'Paciente con miopía leve. Prescripción de lentes correctivos.',
            ],
            [
                'code' => 'EXP-002',
                'patient_id' => 2,
                'observations' => 'Paciente con astigmatismo. Requiere seguimiento cada 6 meses.',
            ],
            [
                'code' => 'EXP-003',
                'patient_id' => 3,
                'observations' => 'Paciente con presbicia. Recomendado lentes progresivos.',
            ],
            [
                'code' => 'EXP-004',
                'patient_id' => 4,
                'observations' => 'Paciente con cataratas incipientes. Seguimiento cada 3 meses.',
            ],
        ];

        foreach ($clinicalRecords as $recordData) {
            ClinicalRecord::create($recordData);
        }

        // Crear citas
        $appointments = [
            [
                'patient_id' => 1,
                'doctor_id' => 1,
                'date' => now()->addDays(5),
                'time' => '09:00:00',
                'status' => 'pendiente',
            ],
            [
                'patient_id' => 2,
                'doctor_id' => 2,
                'date' => now()->addDays(7),
                'time' => '14:00:00',
                'status' => 'pendiente',
            ],
            [
                'patient_id' => 3,
                'doctor_id' => 3,
                'date' => now()->addDays(3),
                'time' => '10:30:00',
                'status' => 'pendiente',
            ],
            [
                'patient_id' => 4,
                'doctor_id' => 1,
                'date' => now()->subDays(2),
                'time' => '16:00:00',
                'status' => 'completada',
            ],
        ];

        foreach ($appointments as $appointmentData) {
            Appointment::create($appointmentData);
        }

        // Crear pagos
        $payments = [
            [
                'appointment_id' => 4,
                'amount' => 1500.00,
                'type' => 'consulta',
            ],
        ];

        foreach ($payments as $paymentData) {
            Payment::create($paymentData);
        }
    }
}

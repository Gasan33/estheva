<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\doctors;
use App\Models\services;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

use Faker\Factory as Faker;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Retrieve some users, doctors, and services to associate with appointments
        $patients = User::pluck('id')->toArray(); // Assuming 'users' table contains patients
        $doctors = Doctor::pluck('id')->toArray(); // Assuming 'doctors' table contains doctors
        $services = services::pluck('id')->toArray(); // Assuming 'services' table contains services

        // Seed 10 example appointments
        foreach (range(1, 10) as $index) {
            Appointment::create([
                'user_id' => $faker->randomElement($patients),
                'doctor_id' => $faker->randomElement($doctors),
                'service_id' => $faker->randomElement($services),
                'appointment_date' => $faker->date(),
                'appointment_time' => $faker->time(),
                'status' => $faker->randomElement(['pending', 'completed', 'canceled']),
                'notes' => $faker->sentence,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalReports;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Services;
use Faker\Factory as Faker;

class MedicalReportsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Assuming you have some patients, doctors, and services already in the database.
        // You can adjust the logic to fit how your relationships are set up.

        $patients = User::where('role', 'patient')->pluck('id');
        $doctors = Doctor::pluck('id');
        $services = Services::pluck('id');

        foreach (range(1, 10) as $index) { // Change 10 to however many records you want to generate
            MedicalReports::create([
                'patient_id' => $faker->randomElement($patients),
                'doctor_id' => $faker->randomElement($doctors),
                'service_id' => $faker->randomElement($services),
                'report_date' => $faker->dateTimeThisYear(),
                'report_details' => $faker->text(),
                'attachments' => json_encode([$faker->imageUrl(), $faker->imageUrl()]), // Example image URLs for attachments
            ]);
        }
    }
}

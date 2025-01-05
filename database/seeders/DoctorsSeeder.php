<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Address;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\MedicalReports;
use App\Models\Review;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class DoctorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a dummy user to associate with the doctor
        // $user = User::factory()->create(); // Assuming you have a User factory

        // Create a dummy doctor associated with the user
        $doctor = Doctor::create([
            'user_id' => 1,
            'specialty' => 'Cardiologist',
            'certificate' => 'MD, FACC',
            'university' => 'Harvard University',
            'patients' => 150,
            'exp' => 12,
            'about' => 'Experienced cardiologist with over 12 years of practice.',
            'home_based' => (bool) rand(0, 1),
        ]);

        // Add dummy addresses
        $doctor->addresses()->create([
            'user_id' => 1,
            'addressable_id' => $doctor->id,
            'addressable_type' => 'App\Models\Doctor',
            'address_line_1' => '456 Health Ave.',
            'address_line_2' => 'Suite 12',
            'city' => 'Los Angeles',
            'state' => 'CA',
            'postal_code' => '90001',
            'country' => 'USA',
            'latitude' => 34.0522,
            'longitude' => -118.2437,
            'is_primary' => false,
        ]);

        // Add availability
        Availability::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
        ]);

        Availability::create([
            'doctor_id' => $doctor->id,
            'day_of_week' => 'Tuesday',
            'start_time' => '10:00:00',
            'end_time' => '16:00:00',
        ]);

        // Add dummy appointments
        // Appointment::create([
        //     'doctor_id' => $doctor->id,
        //     'patient_id' => 1, // Assuming you have a patient model
        //     'appointment_time' => '2024-12-25 10:00:00',
        //     'status' => 'scheduled',
        // ]);

        // // Add dummy medical report
        // MedicalReports::create([
        //     'doctor_id' => $doctor->id,
        //     'patient_id' => 1, // Assuming you have a patient model
        //     'report_details' => 'Patient is showing signs of heart disease.',
        // ]);

        // Add dummy reviews
        Review::create([
            'patient_id' => 1,
            'doctor_id' => $doctor->id,
            'rating' => 5,
            'review_text' => 'The doctor was very helpful and knowledgeable.',
        ]);
    }
}

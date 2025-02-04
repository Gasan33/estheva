<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Treatment;
use Illuminate\Database\Seeder;

class DoctorTreatmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $treatments = Treatment::all();
        $doctors = Doctor::all();

        // Assign treatments to doctors randomly
        foreach ($doctors as $doctor) {
            $doctor->treatments()->attach(
                $treatments->random(rand(1, $treatments->count()))->pluck('id')->toArray()
            );
        }
    }
}

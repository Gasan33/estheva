<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Services;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DoctorServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = Services::all();
        $doctors = Doctor::all();

        // Assign services to doctors randomly
        foreach ($doctors as $doctor) {
            $doctor->services()->attach(
                $services->random(rand(1, $services->count()))->pluck('id')->toArray()
            );
        }
    }
}

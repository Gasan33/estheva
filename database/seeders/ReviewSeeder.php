<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Review::create([
            'patient_id' => 1,
            'treatment_id' => 9,
            'rating' => rand(1, 5),
            'review_text' => "Review for the tested treatment",
        ]);
    }
}

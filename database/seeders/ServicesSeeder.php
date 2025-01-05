<?php

namespace Database\Seeders;

use App\Models\categories;
use App\Models\doctors;
use App\Models\Review;
use App\Models\Services;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        foreach (range(1, 10) as $index) {
            $service = Services::create([
                'name' => 'Service ' . $index,
                'description' => 'Description for Service ' . $index,
                'price' => rand(100, 1000),
                'images' => ['image1.jpg', 'image2.jpg'],
                'home_based' => (bool) rand(0, 1),
                'video' => '',
                'duration' => rand(30, 120),
                'benefits' => ['Benefit 1', 'Benefit 2'],
                'discount_value' => rand(5, 50),
                'discount_type' => ['percentage', 'fixed'][array_rand(['percentage', 'fixed'])],
                'category_id' => 2,
            ]);


            Review::create([
                'patient_id' => 1,
                'service_id' => $service->id,
                'rating' => rand(1, 5),
                'review_text' => 'Review 1 for Service ' . $index,
            ]);

            Review::create([
                'patient_id' => 1,
                'service_id' => $service->id,
                'rating' => rand(1, 5),
                'review_text' => 'Review 2 for Service ' . $index,
            ]);
        }
    }
}

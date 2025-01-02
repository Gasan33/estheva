<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Advertisements;

class AdvertisementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $advertisements = [
            [
                'service_id' => 1,
                'ad_title' => 'Summer Sale',
                'ad_description' => 'Get 50% off on all our services this summer!',
                'ad_picture' => 'summer_sale.jpg',
                'start_date' => '2024-06-01',
                'end_date' => '2024-06-30',
            ],
            [
                'service_id' => 2,
                'ad_title' => 'New Year Discount',
                'ad_description' => 'Celebrate the new year with amazing offers!',
                'ad_picture' => 'new_year_discount.jpg',
                'start_date' => '2024-12-25',
                'end_date' => '2025-01-01',
            ],
            [
                'service_id' => 3,
                'ad_title' => 'Exclusive Offer',
                'ad_description' => 'Avail exclusive discounts for a limited time.',
                'ad_picture' => 'exclusive_offer.jpg',
                'start_date' => '2024-11-01',
                'end_date' => '2024-11-15',
            ],
        ];

        foreach ($advertisements as $ad) {
            Advertisements::create($ad);
        }
    }
}

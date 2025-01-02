<?php

namespace Database\Seeders;

use App\Models\PromoCodes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromoCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PromoCodes::create([
            'code' => 'DISCOUNT50',
            'discount_value' => 50,
            'discount_type' => 'fixed',
            'expiration_date' => '2025-01-01',
            'usage_limit' => 50,
            'status' => 'active'
        ]);
    }
}

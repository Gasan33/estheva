<?php

namespace Database\Seeders;

use App\Models\Addresses;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Addresses::create([
            'user_id' => 1,
            'addressable_id' => 1,
            'addressable_type' => 'App\Models\User',
            'address_line_1' => '123 Main St.',
            'address_line_2' => 'Apt 4B',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'USA',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'is_primary' => true,
        ]);

        // Create a secondary address for the doctor
        Addresses::create([
            'user_id' => 1, // Assuming doctor has a user_id to reference
            'addressable_id' => 1,
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
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create 10 fake users
        foreach (range(1, 10) as $index) {
            User::create([
                'name' => $faker->name,
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'password' => Hash::make('password'), // default password
                // Add any additional attributes as needed, such as roles, verification dates, etc.
            ]);
        }

        // Optionally, create an admin user
        User::create([
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'phone_number' => $faker->phoneNumber,
            'password' => Hash::make('adminpassword'), // default admin password
            'role' => 'admin',
        ]);
    }
}

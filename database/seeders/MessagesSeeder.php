<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Messages;
use Faker\Factory as Faker;
use App\Models\User;

class MessagesSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get the user IDs (assuming you have users in the database)
        $userIds = User::pluck('id')->toArray();

        // Create a set number of messages, for example 50
        foreach (range(1, 50) as $index) {
            // Randomly pick sender and receiver
            $senderId = $faker->randomElement($userIds);
            $receiverId = $faker->randomElement(array_diff($userIds, [$senderId])); // Ensure sender is not the same as receiver

            // Insert a new message
            Messages::create([
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message_text' => $faker->sentence(),
                'is_read' => $faker->boolean(), // Randomly true or false
            ]);
        }
    }
}

<?php

namespace App\Services;
use GuzzleHttp\Client;
class NotificationService
{

    public static function sendNotification($deviceToken)
    {
        $client = new Client();

        $response = $client->post('https://fcm.googleapis.com/fcm/send', [
            'headers' => [
                'Authorization' => 'key=your-server-key',  // Replace with your Firebase server key
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'to' => $deviceToken,  // Device token of the target device
                'notification' => [
                    'title' => 'Notification Title',
                    'body' => 'Notification body message.',
                ],
            ],
        ]);

        $responseBody = $response->getBody();
    }


}
<?php

namespace App\Services;

use App\Models\User;
use App\Models\VerificationCode;
use Carbon\Carbon;

class SMSService
{
    public static function sendOtp(User $user)
    {
        // $otp = app()->environment() == 'local' ? '11111' : strval(rand(10000, 99999));
        $otp = strval(rand(10000, 99999));

        $verificationCode = $user->lastVerificationCode();

        if ($verificationCode && Carbon::now()->isBefore($verificationCode->expire_at)) {
            SMSService::sendSMS($user->phone_number, $verificationCode);
            SMSService::sendWhatsappMessage($user->phone_number, $verificationCode);
            return $verificationCode;
        }

        $verificationCode = VerificationCode::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expire_at' => app()->environment() == 'local' ? Carbon::now()->addDay() : Carbon::now()->addMinute()
        ]);
        SMSService::sendSMS($user->phone_number, $verificationCode);
        SMSService::sendWhatsappMessage($user->phone_number, $verificationCode);
        return $verificationCode;
    }

    public static function sendSMS($phone, $template)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://rpqg9p.api.infobip.com/sms/2/text/advanced',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // CURLOPT_POSTFIELDS => '{"messages":[{"destinations":[{"to":"' . $phone . '"}],"from":"InfoSMS","text":"' . $template . '"}]}',
            CURLOPT_POSTFIELDS => '{"messages":[{"destinations":[{"to":"971545671677"}],"from":"ServiceSMS","text":"Congratulations on sending your first message. Go ahead and check the delivery report in the next step."}]}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: App 022633e51b5eb204f1b69a28efcdb987-9ebdc074-b0ef-4eb3-82d0-ebef6b0b6085',
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }

    public static function sendWhatsappMessage($phone, $template)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://rpqg9p.api.infobip.com/whatsapp/1/message/template',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            // CURLOPT_POSTFIELDS => '{"messages":[{"destinations":[{"to":"' . $phone . '"}],"from":"InfoSMS","text":"' . $template . '"}]}',
            CURLOPT_POSTFIELDS => '{"messages":[{"from":"447860099299","to":"' . $phone . '","messageId":"fdef5ac9-670e-4bdb-91a9-af2230f2dbdd","content":{"templateName":"test_whatsapp_template_en","templateData":{"body":{"placeholders":["Gasan"]}},"language":"en"}}]}',
            CURLOPT_HTTPHEADER => array(
                'Authorization: App 022633e51b5eb204f1b69a28efcdb987-9ebdc074-b0ef-4eb3-82d0-ebef6b0b6085',
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }
}

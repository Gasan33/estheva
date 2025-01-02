<?php

namespace App\Services;

class AgoraService
{
    const ROLE_PUBLISHER = 1;
    const ROLE_SUBSCRIBER = 2;
    const ROLE_ADMIN = 101;

    /**
     * Generate Agora RTC token
     *
     * @param string $channelName
     * @param int $uid
     * @param int $role
     * @param int $expireTimeInSeconds
     * @return string
     */
    public static function generateToken($channelName, $uid, $role = self::ROLE_SUBSCRIBER, $expireTimeInSeconds = 3600)
    {
        $appId = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERTIFICATE');

        $currentTimestamp = time();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

        // Token structure
        $uid = (int) $uid;
        $str = $appId . $channelName . $uid . $privilegeExpiredTs . $role;

        $token = self::generateAgoraToken($str, $appCertificate);

        return $token;
    }

    /**
     * Generate the token using the HMAC-SHA1 algorithm
     *
     * @param string $str
     * @param string $appCertificate
     * @return string
     */
    private static function generateAgoraToken($str, $appCertificate)
    {
        // Prepare signature
        $signature = hash_hmac('sha1', $str, $appCertificate, true);
        $base64Signature = base64_encode($signature);

        return $base64Signature;
    }
}

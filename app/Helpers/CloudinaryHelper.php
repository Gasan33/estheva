<?php
namespace App\Helpers;

use Cloudinary\Cloudinary;

class CloudinaryHelper
{
    public static function upload($file, $folder = 'uploads')
    {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
            'folder' => $folder,
        ]);

        return $result['secure_url'] ?? null;
    }


    public static function uploadVideo($file, $folder = 'uploads')
    {
        $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));

        $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
            'resource_type' => 'video', // Specify that this is a video upload
            'folder' => $folder,
        ]);

        return $result['secure_url'] ?? null;
    }
}

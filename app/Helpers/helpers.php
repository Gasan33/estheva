<?php

use App\Services\ApiResponse;

if (!function_exists('api')) {

    function api()
    {
        return new ApiResponse();
    }
}

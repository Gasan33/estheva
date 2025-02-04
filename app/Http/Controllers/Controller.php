<?php

namespace App\Http\Controllers;

use App\Services\ApiResponse;

abstract class Controller
{
    public function api()
    {
        return new ApiResponse();

    }
}






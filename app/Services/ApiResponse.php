<?php

namespace App\Services;

class ApiResponse
{
    public function response($data, $message, $code)
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
            'code' => $code
        ], $code);
    }

    public function created($data = [], $message = 'created successfully', $code = 201)
    {
        return $this->response($data, $message, $code);
    }

    public function success($data = [], $message = 'success', $code = 200)
    {
        return $this->response($data, $message, $code);
    }

    public function error($message = 'error', $code = 500)
    {
        return $this->response([], $message, $code);
    }

    public function validation($data = [], $message = 'validation error')
    {
        return $this->response([], $message, 422);
    }

    public function notFound($message = 'error', $code = 404)
    {
        return $this->response([], $message, $code);
    }

    public function unauth($message = 'Invalid credentials, please try again.', $code = 401)
    {
        return $this->response([], $message, $code);
    }
    public function forbidden($message = 'Access denied. Admins only.', $code = 403)
    {
        return $this->response([], $message, $code);
    }
}

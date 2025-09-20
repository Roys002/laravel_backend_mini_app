<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    public static function error($message = 'Error', $code = 400, $errors  = null)
    {
        return response()->json([
            'status'  => 'error',
            'message' => $message,
            'errors'    => $errors,
        ], $code);
    }

    public static function validationError($errors, string $message = 'Validation failed')
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], 422);
    }
}

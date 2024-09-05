<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ResponseTrait
{
    /**
     * Response success
     * @param array|object $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function responseSuccess($data, $message , $code):JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], );
    }

    /**
     * Response error
     * @param string $message
     * @param int $code
     * @return JsonResponse
     * @throws \Exception
     * 
     */
    public function responseError($error, $message, $code):JsonResponse
    {
        return response()->json([
            'status' => $code,
            'message' => $message,
            'error' => $error,   
        ]);
    }
}
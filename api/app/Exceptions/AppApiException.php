<?php

namespace App\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class AppApiException extends HttpResponseException
{
    /**
     * @param string $message
     */
    function __construct($message, $response_code, $code = null)
    {
        if ($code == null) {
            $code = Response::HTTP_OK;
        }
        $json = response()->json([
            'status' => false,
            'statusCode' => intval($code),
            'message' => trans($message),
            'responseCode' => $response_code,
            'response' => null
        ], $code);
        parent::__construct($json);
    }
}
<?php

namespace App\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ApiException extends HttpResponseException
{
    protected $message;
    /**
     * @param string $message
     */
    function __construct($message, $code = null)
    {
        if ($code == null) {
            $code = Response::HTTP_OK;
        }
        $this->message = trans($message);
        $json = response()->json([
            'status' => false,
            'statusCode' => intval($code),
            'message' => $this->message,
            'response' => null
        ], $code);
        parent::__construct($json);
    }
}
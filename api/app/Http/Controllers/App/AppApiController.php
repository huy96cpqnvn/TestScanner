<?php


namespace App\Http\Controllers\App;

use App\Exceptions\AppApiException;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class AppApiController extends Controller
{
  public function validate(\Illuminate\Http\Request $request, array $rules, array $messages = [], array $customAttributes = [], $response_code = '01', $code = Response::HTTP_BAD_REQUEST)
  {
    try {
      return parent::validate($request, $rules, $messages, $customAttributes, $code);
    } catch (ApiException $e) {
      throw new AppApiException($e->getMessage(), $response_code, $code);
    }
  }

  public function error($message, $response_code = '01', $code = Response::HTTP_BAD_REQUEST)
  {
    throw new AppApiException($message, $response_code, $code);
  }

  public function response($status, $code, $response = null, $message = '')
  {
    if ($code == Response::HTTP_OK) {
      $message = 'Success';
      $response_code = '00';
    } else {
      $status = false;
      $response_code = '01';
    }
    return array(
      'status' => $status,
      'statusCode' => intval($code),
      'message' => trans($message),
      'responseCode' => $response_code,
      'response' => $response,
    );
  }
}

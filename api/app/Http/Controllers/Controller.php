<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Rules\CaptchaRule;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    public function validate(\Illuminate\Http\Request $request, array $rules, array $messages = [], array $customAttributes = [], $code = null)
    {
        try {
            $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            // return $request->only(array_keys($rules));
            return $this->extractInputFromRules($request, $rules);
            //return parent::validate($request, $rules, $messages, $customAttributes);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $code);
        }
    }

    public function validateHasCaptcha(\Illuminate\Http\Request $request, array $rules, array $messages = [], array $customAttributes = [], $code = null)
    {
        $rules_validate = $rules;
        $rules_validate['params.captcha_id'] = 'required|integer';
        $rules_validate['params.captcha_code'] = ['required', new CaptchaRule($request->input('params.captcha_id'))];
        try {
            $validator = Validator::make($request->all(), $rules_validate, $messages, $customAttributes);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            return $this->extractInputFromRules($request, $rules);            
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), $code);
        }
    }

    protected function extractInputFromRules(Request $request, array $rules)
    {
        if ($this->_hasInputArrayObject($rules)) {
            $inputs = parent::extractInputFromRules($request, $rules);            
        } else {
            $inputs = $request->only(array_keys($rules));
        }        
        return $inputs;
    }

    protected function _hasInputArrayObject($rules)
    {
        foreach ($rules as $key => $rule) {
            if (strpos($key, '*') !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param mixed $ability
     * @param mixed|array $arguments
     * @return \Illuminate\Auth\Access\Response
     * @throws ApiException
     */
    public function authorize($ability, $arguments = [])
    {
        try {
            return parent::authorize($ability, $arguments);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), Response::HTTP_FORBIDDEN);
        }
    }

    public function error($message, $code = null)
    {
        throw new ApiException($message, $code);
    }

    public function responseSuccess($response = null)
    {
        return $this->response(true, Response::HTTP_OK, $response);
    }

    public function response($status, $code, $response = null, $message = '')
    {
        if ($code == Response::HTTP_OK) {
            $message = 'Success';
        } else {
            $status = false;
        }
        return array(
            'status' => $status,
            'statusCode' => intval($code),
            'message' => trans($message),
            'response' => $response,
        );
    }
}

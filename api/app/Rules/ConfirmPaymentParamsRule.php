<?php

namespace App\Rules;

use App\Models\Order;
use Illuminate\Contracts\Validation\Rule;

class ConfirmPaymentParamsRule implements Rule
{
  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {    
    if (is_array($value) && array_key_exists('my_payment_order', $value) && intval($value['my_payment_order']) > 0) {
      return true;
    }
    return false;
  }

  /**
   * Get the validation error message.
   *
   * @return string
   */
  public function message()
  {
    return trans('Tham số xác nhận thanh toán không hợp lệ');
  }
}

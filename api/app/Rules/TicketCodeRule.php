<?php

namespace App\Rules;

use App\Services\Tickets\TicketService;
use Illuminate\Contracts\Validation\Rule;

class TicketCodeRule implements Rule
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
    if (preg_match('/^[A-Z][A-Z0-9]{2}([A-Z0-9]{3})([A-Z0-9]{6})$/', $value, $matches)) {      
      if (TicketService::checkCertificateCode($value)) {
        return true;
      }
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
    return trans('Vé không hợp lệ');
  }
}

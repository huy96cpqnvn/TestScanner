<?php

namespace App\Rules;

use App\Models\Captcha;
use Illuminate\Contracts\Validation\Rule;

class CaptchaRule implements Rule
{
  protected $captcha_id;

  public function __construct($captcha_id = '')
  {
    $this->captcha_id = $captcha_id;
  }

  /**
   * Determine if the validation rule passes.
   *
   * @param  string  $attribute
   * @param  mixed  $value
   * @return bool
   */
  public function passes($attribute, $value)
  {
    if (preg_match('/^([a-zA-Z0-9]+)$/', $value, $matches)) {
      return $this->_validate($matches[1]);
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
    return trans('Mã xác nhận không hợp lệ');
  }

  public function getCaptcha($width, $height, $length = 6, $font_size = 20)
  {
    $captcha_code = $this->_getRandomString($length);
    $captcha_image = $this->_makeCaptchaImage($captcha_code, $width, $height, $font_size);
    $captcha_id = $this->_saveCaptcha($captcha_code);
    return array(
      'captcha_image' => $captcha_image,
      'captcha_id' => $captcha_id
    );
  }

  private function _validate($captcha_code)
  {
    $captcha = Captcha::find($this->captcha_id);
    if ($captcha && $captcha->code === $this->_encryptCaptchaCode($captcha_code)) {
      $captcha->delete();
      return true;
    }
    return false;
  }

  private function _encryptCaptchaCode($captcha_code)
  {
    return md5($captcha_code);
  }

  private function _saveCaptcha($captcha_code)
  {
    Captcha::deleteCaptchaTimeout();
    $model = Captcha::create([
      'code' => $this->_encryptCaptchaCode($captcha_code)
    ]);
    if ($model) {
      return $model->id;
    }
    return 0;
  }

  private function _makeCaptchaImage($captcha_code, $width, $height, $font_size)
  {
    // Create the image
    $im = imagecreatetruecolor($width, $height);

    // Create some colors
    $white  = imagecolorallocate($im, 255, 255, 255);
    $grey   = imagecolorallocate($im, 229, 227, 227);
    $black  = imagecolorallocate($im, 0, 0, 0);
    $red  = imagecolorallocate($im, 182, 32, 29);
    imagefilledrectangle($im, 0, 0, $width, $height, $white);

    //ADD NOISE - DRAW background squares
    // $square_count = 6;
    // for ($i = 0; $i < $square_count; $i++) {
    //   $cx = rand(0, $width);
    //   $cy = (int)rand(0, $width / 2);
    //   $h  = $cy + (int)rand(0, $height / 5);
    //   $w  = $cx + (int)rand($width / 3, $width);
    //   imagefilledrectangle($im, $cx, $cy, $w, $h, $white);
    // }

    // ADD NOISE - DRAW ELLIPSES
    // $ellipse_count = 5;
    // for ($i = 0; $i < $ellipse_count; $i++) {
    //   $cx = (int)rand(-1 * ($width / 2), $width + ($width / 2));
    //   $cy = (int)rand(-1 * ($height / 2), $height + ($height / 2));
    //   $h  = (int)rand($height / 2, 2 * $height);
    //   $w  = (int)rand($width / 2, 2 * $width);
    //   imageellipse($im, $cx, $cy, $w, $h, $grey);
    // }

    // Replace path by your own font path
    $font = dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../resources/fonts/arialbd.ttf';

    $y = round(($height + $font_size) / 2);
    $x = 10;
    // Add some shadow to the text
    imagettftext($im, $font_size, 0, $x + 1, $y + 1, $grey, $font, $captcha_code);

    // Add the text
    imagettftext($im, $font_size, 0, $x, $y, $red, $font, $captcha_code);

    // Using imagepng() results in clearer text compared with imagejpeg()
    ob_start();
    imagepng($im);
    $image_data = ob_get_clean();
    imagedestroy($im);

    return 'data:image/png;base64,' . base64_encode($image_data);
  }

  private function _getRandomString($length)
  {
    // $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
    $characters_length = strlen($characters);
    $captcha = [];
    for ($i = 0; $i < $length; $i++) {
      $char = substr($characters, rand(0, $characters_length - 1), 1);
      if (!in_array($char, $captcha)) {
        $captcha[] = $char;
      } else {
        $i--;
      }
    }
    return implode('', $captcha);
  }
}

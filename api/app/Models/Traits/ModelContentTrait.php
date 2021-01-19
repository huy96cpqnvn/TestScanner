<?php

namespace App\Models\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use KubAT\PhpSimple\HtmlDomParser;

trait ModelContentTrait
{
  public $images_content = [];
  public $old_images_content = [];
  protected $fields_content = [];

  protected static function boot()
  {
    parent::boot();
    self::saving(function ($model) {
      $model->processFieldsContentBeforeSave($model);
    });
    self::saved(function ($model) {
      $model->processFieldsContentAfterSave($model);
    });
    self::deleting(function ($model) {
      $model->processFieldsContentBeforeDelete($model);
    });
    self::deleted(function ($model) {
      $model->processFieldsContentAfterDelete($model);
    });
  }

  public function setAttribute($key, $value)
  {
    if ($this->getKey() != null && array_key_exists($key, $this->fields_content)) {
      if (!array_key_exists($key, $this->old_images_content)) {
        $this->old_images_content[$key] = $this->_getImagesPath($this->$key);
      }
    }
    parent::setAttribute($key, $value);
  }

  public function processFieldsContentAfterDelete()
  {
    if (!empty($this->old_images_content)) {
      foreach ($this->old_images_content as $key => $old_files) {
        if (!empty($old_files)) {
          foreach ($old_files as $old_file) {
            if (Storage::disk('local')->exists($old_file)) {
              Storage::disk('local')->delete($old_file);
            }
          }
        }
      }
    }
  }

  public function processFieldsContentBeforeDelete()
  {
    if (!empty($this->fields_content)) {
      foreach ($this->fields_content as $field => $options) {
        $this->old_images_content[$field] = $this->_getImagesPath($this->$field);
      }
    }
  }

  public function processFieldsContentAfterSave()
  {
    if (!empty($this->fields_content)) {
      foreach ($this->fields_content as $field => $options) {
        $this->images_content[$field] = $this->_getImagesPath($this->$field);
      }
      if (!empty($this->old_images_content)) {
        foreach ($this->old_images_content as $key => $old_files) {
          if (!empty($old_files)) {
            foreach ($old_files as $old_file) {
              if (!isset($this->images_content[$key]) || !in_array($old_file, $this->images_content[$key])) {
                if (Storage::disk('local')->exists($old_file)) {
                  Storage::disk('local')->delete($old_file);
                }
              }
            }
          }
        }
      }
    }
  }

  public function processFieldsContentBeforeSave()
  {
    if (!empty($this->fields_content)) {
      foreach ($this->fields_content as $field => $options) {
        $this->$field = $this->_getNewContent($this->$field, $options);
      }
    }
  }

  protected function _getNewContent($content, $options)
  {
    $images_url = $this->_getImagesUrl($content);    
    if (!empty($images_url)) {      
      foreach ($images_url as $image_url) {
        $max_width = @$options['max_width'];
        $image_content_url = $this->_getImageContentUrl($image_url, $max_width);
        if ($image_content_url !== false) {
          $content = str_replace($image_url, $image_content_url, $content);
        } else {
          throw new Exception('Có lỗi khi tải file');
        }
      }
    }
    return $content;
  }

  protected function _getImageContentUrl($image_url, $max_width)
  {
    if ($this->_isTempImageUrl($image_url)) {      
      $image_temp_path = $this->_getImageTempPath($image_url);      
      if ($image_temp_path != false) {
        try {
          if (intval($max_width) > 0) {
            // resize image
            $image_obj = Image::make(storage_path('/app/' . $image_temp_path));
            $image_resize = $this->_getImageResize($image_obj, $max_width);
            $image_obj->resize($image_resize['width'], $image_resize['height'])->save();
          }
          //---------------                    
          $image_content_path = $this->_getImageContentPath($image_url);
          if (Storage::disk('local')->move($image_temp_path, $image_content_path)) {
            return getFileUrl($image_content_path);
          } else {
            return false;
          }
        } catch (Exception $e) {
          return false;
        }        
      }
      return false;
    }
    return $image_url;
  }

  protected function _getImageResize($image_obj, $max_width)
  {
    $width = $image_obj->width();
    $height = $image_obj->height();
    if ($width > $max_width) {
      $height = round($height * $max_width / $width, 0);
      $width = $max_width;
    }
    return [
      'width' => $width,
      'height' => $height,
    ];
  }

  protected function _getImagesPath($content)
  {
    $result = [];
    $images_url = $this->_getImagesUrl($content);
    if (!empty($images_url)) {
      foreach ($images_url as $image_url) {
        $image_path = getFilePath($image_url);
        if ($image_path != false) {
          $result[] = $image_path;
        }
      }
    }
    return $result;
  }

  protected function _getImagesUrl($content)
  {
    $result = [];
    $dom = HtmlDomParser::str_get_html($content);
    $imgs = $dom->find('img');
    if (!empty($imgs)) {
      foreach ($imgs as $img) {        
        $result[] = $img->src;
      }
    }    
    return $result;
  }

  protected function _isTempImageUrl($image_url)
  {
    if (isAppUrl($image_url) && strpos($image_url, '/temp/') !== false) {
      return true;
    }
    return false;
  }

  protected function _getImageTempPath($image_url)
  {
    if (preg_match('/([^\/]+)$/', $image_url, $matches)) {
      return 'public' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $matches[1];
    }
    return false;
  }

  protected function _getImageContentPath($image_url)
  {
    $extension = getFileExtension($image_url);
    return 'public' . DIRECTORY_SEPARATOR . $this->table . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . uniqid() . '.' . $extension;
  }
}

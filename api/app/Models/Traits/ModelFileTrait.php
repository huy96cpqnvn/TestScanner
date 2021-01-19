<?php

namespace App\Models\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

trait ModelFileTrait
{
  public $files = [];
  public $old_files = [];
  public $fields_file = [];

  protected static function boot()
  {
    parent::boot();
    self::saving(function ($model) {
      $model->processFilesUploadBeforeSave($model);
    });
    self::saved(function ($model) {
      $model->processFilesUploadAfterSave($model);
    });
    self::deleting(function ($model) {
      $model->processFilesUploadBeforeDelete($model);
    });
    self::deleted(function ($model) {
      $model->processFilesUploadAfterDelete($model);
    });
  }

  public function setAttribute($key, $value)
  {
    if ($this->getKey() != null && array_key_exists($key, $this->fields_file)) {
      if (!array_key_exists($key, $this->old_files)) {
        $this->old_files[$key] = $this->$key;
      }
    }
    parent::setAttribute($key, $value);
  }

  public function getFileUrl($file_path)
  {
    return getFileUrl($file_path);
  }

  public function processFilesUploadAfterDelete()
  {
    if (!empty($this->fields_file)) {
      foreach ($this->fields_file as $field => $options) {
        if ($options['type'] == 'image') {
          $this->_deleteOldImage($this->old_files[$field]);
        }
      }
    }
  }

  public function processFilesUploadBeforeDelete()
  {
    if (!empty($this->fields_file)) {
      foreach ($this->fields_file as $field => $options) {
        if (@$options['type'] == 'image') {
          $this->old_files[$field] = $this->$field;
        }
      }
    }
  }

  public function processFilesUploadAfterSave()
  {
    if (!empty($this->fields_file)) {
      foreach ($this->fields_file as $field => $options) {
        if ($options['type'] == 'image') {
          $this->_deleteOldImage(@$this->old_files[$field], $this->$field);
        }
      }
    }
  }

  public function processFilesUploadBeforeSave()
  {
    if (!empty($this->fields_file)) {
      foreach ($this->fields_file as $field => $options) {
        if (isset($this->$field) && $this->_isFileUpload($this->$field)) {
          if (@$options['type'] == 'image') {
            $image_path = $this->_uploadImage($this->$field, @$options['width'], @$options['height']);
            if ($image_path !== false) {
              $this->$field = $image_path;
            } else {
              throw new Exception('Có lỗi khi tải file');
            }
          }
        }
      }
    }
  }

  protected function _deleteOldImage($old_image, $new_image = '')
  {
    if (trim($old_image) != '' && trim($old_image) != trim($new_image)) {
      if (Storage::disk('local')->exists($old_image) && !Storage::disk('local')->delete($old_image)) {
        return false;
      }
    }
    return true;
  }

  protected function _isFileUpload($files)
  {
    if (is_array($files) && !empty($files)) {
      return true;
    }
    return false;
  }

  protected function _uploadImage($images, $width, $height)
  {
    if (is_array($images) && !empty($images) && isset($images[0])) {
      $image = $images[0];
      if (intval($image['id']) == 0) {
        try {
          if (intval($width) > 0 && intval($height) > 0) {
            // resize image
            Image::make(storage_path('/app/' . $image['path']))->resize($width, $height)->save();
          }
          //---------------                    
          $image_path = $this->_getImagePath($image['name']);
          if (Storage::disk('local')->move($image['path'], $image_path)) {
            return $image_path;
          } else {
            return false;
          }
        } catch (Exception $e) {
          return false;
        }
      }
      return $image['path'];
    }
    return '';
  }

  protected function _getImagePath($image_name)
  {
    $extension = getFileExtension($image_name);
    return 'public' . DIRECTORY_SEPARATOR . $this->table . DIRECTORY_SEPARATOR . uniqid() . '.' . $extension;
  }
}

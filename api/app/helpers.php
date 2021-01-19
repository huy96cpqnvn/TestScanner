<?php

use App\Models\UserGroup;
use Illuminate\Support\Facades\Storage;

function currencyFormat($amount)
{
    return number_format($amount, 0, ',', '.');
}

function userGroup() : UserGroup
{
    return auth()->user()->current_user_group;
}

function sessionData($key)
{
    $session_data = auth()->user()->user_token_data;
    if (isset($session_data[$key])) {
        return $session_data[$key];
    }
    return null;
}

function groupIdsChild()
{
    return auth()->user()->current_user_group->group->getChildIds();
}

function groupIdsSelfAndChild()
{
    return auth()->user()->current_user_group->group->getSelfAndChildIds();
}

function groupIdsBranch()
{
    return auth()->user()->current_user_group->group->getBranchIds();
}

function convertDoubleValue($value, $precision = 2)
{
    return round(floatval($value), $precision);
}

function getIP()
{
    // return '127.0.0.1';
    return request()->ip();
}

//CheckAndTake
function checkAndTake(&$value, $defaultSet = '', $trimFlg = false)
{
    $value = isset($value) ? $value : $defaultSet;
    if ($trimFlg) {
        $value = trim($value);
    }
    return $value;
}

function userId()
{
    if (isset(auth()->user()->id)) {
        return auth()->user()->id;
    }
    return 0;
}

function getFileUrl($file_path)
{
    if (trim($file_path) != '' && preg_match('/^(public)/', $file_path) && Storage::disk('local')->exists($file_path)) {
        $image = str_replace('public', '', $file_path);
        $image = str_replace('\\', '/', $image);
        return env('APP_URL').'/assets' . $image;
    }
    return '';
}

function isAppUrl($file_url)
{
    if (substr($file_url, 0, strlen(env('APP_URL'))) == env('APP_URL')) {
        return true;
    }
    return false;
}

function getFilePath($file_url)
{
    if (isAppUrl($file_url)) {
        $file_path = str_replace(env('APP_URL').'/assets/', '', $file_url);
        $file_path = str_replace('/', DIRECTORY_SEPARATOR, $file_path);
        $file_path = 'public' . DIRECTORY_SEPARATOR . $file_path;
        return $file_path;
    }
    return false;    
}

function getFileExtension($file_name)
{
    return substr($file_name, strrpos($file_name, '.') + 1);
}

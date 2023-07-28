<?php

use Illuminate\Support\Str;

if (!function_exists('lfIcon')) {
    function lfIcon($name, $width = 18, $height = 0, $viewBox = "0 0 24 24", $attribute = null)
    {
        $version = intval(config('app.asset_version'));
        $env = config("app.env","production");
        $asset = "/assets";
        if($env == "production" && $version>0){
            $asset .= "/$version";
        }

        if ($height == 0) $height = $width;
        return '
            <svg width="' . $width . 'px" height="' . $height . 'px"  viewBox="' . $viewBox . '" class="mcon" ' . $attribute . ' fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                <use xlink:href="'.$asset.'/images/icons.svg#' . $name . '"/>
            </svg>
        ';
    }
}

if(!function_exists("lfHeadLine")){
    function lfHeadLine($str=""){
        $str = Str::replace("/", " ", $str);
        $str = Str::snake($str, "-");
        return Str::headline($str);
    }
}
if(!function_exists("lfCheckLocalhost")){
    function lfCheckLocalhost(){
        $local = ['localhost','127.0.0.1'];
        $serverName = \Illuminate\Support\Facades\Request::server('SERVER_NAME');
        return in_array($serverName,$local);
    }
}



<?php

namespace Hungnm28\LivewireAdmin\Supports;

use Illuminate\Support\Facades\File;

class IconSupport
{

    public static function getIconNames()
    {
        $path = public_path("assets/images/icons.svg");
        $icons = [];
        if (!File::exists($path)) {
            return $icons;
        }
        $str = File::get($path);

        if (preg_match_all('/id=\"([a-z0-9-]*)\"/', $str, $arr)) {
            foreach ($arr[1] as $name) {
                $icons[$name] = $name;
            }
        }
        return $icons;
    }

    private function getContent()
    {
        $path = public_path("assets/images/icons.svg");
        $icons = [];
        if (!File::exists($path)) {
            return $icons;
        }
        $str = File::get($path);
        $doms = str_get_html($str);
        foreach ($doms->find("symbol") as $dom) {
            $icons[$dom->id] = $dom->outertext;
        }
        ksort($icons);
        return $icons;
    }

    private function saveFile($data = [])
    {
        $path = public_path("assets/images/icons.svg");
        ksort($data);
        $svg = '<?xml version="1.0" encoding="utf-8"?>
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'
            . implode("\n", $data) .
            '</svg>';
        return File::put($path, $svg);
    }

    public static function addIcon($name, $str, $width = 24, $height = 24)
    {
        $data = (new self())->getContent();
        $data[$name] = '<symbol id="' . $name . '" fill="currentColor" viewBox="0 0 ' . $width . ' ' . $height . '" enable-background="0 0 ' . $width . ' ' . $height . '">' . $str . '</symbol>';
        return (new self())->saveFile($data);
    }
}

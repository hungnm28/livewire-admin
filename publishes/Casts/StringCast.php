<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class StringCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        $value = $this->removeScript($value);
        $value = $this->removeHTML($value);
        $value = $this->removeDoubleSpace($value);
        $value = trim($value);
        return $value;
    }
    private function removeScript($string)
    {
        $string = preg_replace('/<script.*?\>.*?<\/script>/si', ' ', $string);
        $string = preg_replace('/<style.*?\>.*?<\/style>/si', ' ', $string);


        return $string;
    }
    private function removeHTML($string)
    {
        $breaks = array("<br />", "<br>", "<br/>");
        $string = str_replace('&nbsp;', ' ', $string);
        $string = preg_replace('/(?:\s*<br[^>]*>\s*){3,}/s', "<br>", $string);
        $string = str_ireplace($breaks, "\r\n", $string);
        $string = preg_replace('/<script.*?\>.*?<\/script>/si', ' ', $string);
        $string = preg_replace('/<style.*?\>.*?<\/style>/si', ' ', $string);
        $string = preg_replace('/<\/.*?\>/si', "\r\n", $string);
        $string = preg_replace('/<.*?\>/si', ' ', $string);


        return $string;
    }

    function removeDoubleSpace($input)
    {
        $input = str_replace('&nbsp;', ' ', $input);
        $input =preg_replace('/\s\s+/', ' ',$input);
        return $input;
    }
}

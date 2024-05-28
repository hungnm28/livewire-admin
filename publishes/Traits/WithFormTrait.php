<?php

namespace App\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait WithFormTrait
{
    public $record_id;
    public function updated($field)
    {
        $rules = $this->getRules();
        if (isset($rules[$field])) {
            $this->validateOnly($field);
        }
    }

    public function resetForm()
    {
        $this->setFields();
    }

    public function jsonDelete($name,$key){
        Arr::forget($this->$name,$key);
    }
    public function jsonAdd($name,$key,$value){
        $key = Str::slug($key);
        $this->$name[$key] = $value;
    }
    public function pushNotification($type, $message)
    {
        $this->dispatch("toast", compact('type', 'message'));
    }
}

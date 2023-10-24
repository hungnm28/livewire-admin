<?php

namespace Hungnm28\LivewireAdmin\Traits;

trait WithFormTrait
{

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

    public function pushNotification($type, $message)
    {
        $this->dispatch("toast", compact('type', 'message'));
    }
}

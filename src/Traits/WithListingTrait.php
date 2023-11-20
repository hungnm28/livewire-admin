<?php

namespace Hungnm28\LivewireAdmin\Traits;

trait WithListingTrait
{
    public $show, $edit, $create, $delete, $record_id, $render=false;

    public function __construct()
    {
        $this->listeners[] = "resetModal";
        $this->listeners[] = "reRender";
    }

    public function modalCreate()
    {
        $this->resetModal();
        $this->create = true;
        $this->record_id = 0;
    }

    public function modalDelete($id)
    {
        $this->resetModal();
        $this->delete = true;
        $this->record_id = $id;
    }
    public function modalEdit($id)
    {
        $this->resetModal();
        $this->record_id = $id;
        $this->edit = true;
    }

    public function modalShow($id)
    {
        $this->resetModal();
        $this->record_id = $id;
        $this->show = true;
    }

    public function cancelShow()
    {
        $this->record_id = 0;
        $this->show = false;
    }

    public function resetModal()
    {
        $this->show = false;
        $this->create = false;
        $this->edit = false;
        $this->delete = false;
        $this->record_id = 0;
    }
    public function pushNotification($type, $message)
    {
        $this->dispatch("toast", compact('type', 'message'));
    }
    public function reRender(){
        $this->render = !$this->render;
    }
}

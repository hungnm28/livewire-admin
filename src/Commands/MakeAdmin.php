<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-admin {module}';

    protected $description = 'Make Admin';

    public function handle()
    {
        $this->initModule($this->argument("module"));
        $this->makeSetting();

    }

    private function makeSetting()
    {
        $pathSave = $this->getModulepath("Livewire/Settings/Index.php");
         $this->createFile(
            path: $pathSave,
            name: 'Index.php',
            stub: 'Admin/Settings/Index.php.stub'
            , force: true
        );

        $pathSave = $this->getModulepath("Resources/views/livewire/settings/index.blade.php");
        $this->createFile(
            path: $pathSave,
            name: 'index.blade.php',
            stub: 'Admin/Settings/index.blade.php.stub'
            , force: true
        );
        $this->call("la:make-route",["name"=>"Settings","module"=>$this->argument("module")]);
    }

    private function makeIcon()
    {

    }

    private function makeMenu()
    {

    }

    private function makeAdmin()
    {

    }

    private function makePermission()
    {

    }

    private function makeRole()
    {

    }

    private function makeColor()
    {

    }
}
<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;

class PageCommand extends Command
{
    protected $signature = 'la:page {folder} {module} {--model=} {--force} {--type=} {--listType=}';
    protected $description = 'Generate Auth';

    use CommandTrait;

    public function handle()
    {
        $this->init();
//        $this->createClass("FormTrait.php");
//        $this->createView("create.blade.php");
//        $this->createView("edit.blade.php");
//        $this->createView("show.blade.php");
//        $this->createView("index.blade.php");
 //       $this->createClass("Show.php");
 //       $this->createClass("Edit.php");
 //       $this->createClass("Create.php");
        $this->createClass("Index.php");
    }

    private function createClass($name)
    {
        $template = $this->getTemplate("$name.stub");

        $pathSave = $this->livewireClass($name);
        if($template && $pathSave){
            return $this->writeFile($pathSave,$template);
        }
        return false;
    }

    private function createView($name)
    {
        $template = $this->getViewTemplate("$name.stub");

        $pathSave = $this->livewireView($name);
        if($template && $pathSave){
            return $this->writeFile($pathSave,$template);
        }
        return false;
    }
}
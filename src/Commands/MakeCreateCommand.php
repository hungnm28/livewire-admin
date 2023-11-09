<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;

class MakeCreateCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-create {name} {module} {--model=}';

    protected $description = 'Make Create page';

    public function handle()
    {
        $this->info("Make Create page");
        $this->initModule($this->argument("module"));
        $this->initPath($this->argument("name"));
        $this->initModel($this->path);
        $this->createClass();
        $this->createView();
    }

    private function createClass()
    {
        $pathSave = $this->getClassFile("Create.php");
        return $this->createFile(
            path: $pathSave,
            name: "Create.php"
        );
    }

    private function createView()
    {
        $pathSave = $this->getViewFile("create.blade.php");
        $forms = $this->generateForm('create');
        return $this->createFile(
            path: $pathSave
            , name: "create.blade.php"
            , data: [
            'DUMP_MY_FORMS' => implode($this->showNewLine(3), $forms)
        ]
            , force: true
        );
    }


}

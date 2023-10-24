<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;

class MakeEditCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-edit {name} {module} {--model=}';

    protected $description = 'Make Edit page';

    public function handle()
    {
        $this->info("Make Edit page");
        $this->initModule($this->argument("module"));
        $this->initPath($this->argument("name"));
        $this->initModel($this->path);
        $this->createClass();
        $this->createView();
    }

    private function createClass()
    {
        $pathSave = $this->getClassFile("Edit.php");
        return $this->createFile(
            path: $pathSave,
            name: "Edit.php"
        );

    }

    private function createView()
    {
        $pathSave = $this->getViewFile("edit.blade.php");
        $forms = $this->generateForm();
        return $this->createFile(
            path: $pathSave
            , name: "edit.blade.php"
            , data: [
            'DUMP_MY_FORMS' => implode($this->showNewLine(3), $forms)
        ]
            , force: true
        );
    }

}

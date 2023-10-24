<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;

class MakeShowCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-show {name} {module} {--model=}';

    protected $description = 'Make Show page';

    public function handle()
    {
        $this->info("Make Show page");
        $this->initModule($this->argument("module"));
        $this->initPath($this->argument("name"));
        $this->initModel($this->path);
        $this->createClass();
        $this->createView();
    }

    private function createClass()
    {
        $pathSave = $this->getClassFile("Show.php");
        return $this->createFile(
            path: $pathSave,
            name: 'Show.php',
        );
    }

    private function createView()
    {

        $pathSave = $this->getViewFile("show.blade.php");
        $show_fields = $this->generateShowFields();
        return $this->createFile(
            path: $pathSave
            , name: "show.blade.php"
            , data: [
            'DUMP_MY_SHOW_FIELDS' => implode($this->showNewLine(4), $show_fields)
        ]
            , force: true
        );
    }


}

<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;

class MakeIndexCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-index {name} {module} {--model=}';

    protected $description = 'Make Index page';

    public function handle()
    {
        $this->info("Make Index page");
        $this->initModule($this->argument("module"));
        $this->initPath($this->argument("name"));
        $this->initModel($this->path);
        $this->createClass();
        $this->createView();
    }

    private function createClass()
    {
        $pathSave = $this->getClassFile("Index.php");
        $fields = $this->generateListFields();
        return $this->createFile(
            path: $pathSave,
            name: 'Index.php',
            data: [
                "DUMP_MY_ARR_FIELDS" => implode(',' . $this->showNewLine(4), $fields)
            ],
            stub: 'Listing.php.stub'
        );
    }

    private function createView()
    {
        $pathSave = $this->getViewFile("index.blade.php");
        $labels = $this->generateTableLabel();
        $items = $this->generateTableItem();
        return $this->createFile(
            path: $pathSave
            , name: "index.blade.php"
            , data: [
            'DUMP_MY_TABLE_LABELS' => implode($this->showNewLine(5), $labels),
            'DUMP_MY_TABLE_ITEMS' => implode($this->showNewLine(6), $items)
        ]
            , stub: 'listing.blade.php.stub'
            , force: true
        );
    }


}

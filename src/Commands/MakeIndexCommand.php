<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;

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
        $type = select(label: "Select type show:",
            options: ['table' => "Table", 'tree' => 'Tree'], default: 'table'
        );
        switch ($type) {
            case 'tree':
                $this->treeList();
                break;
            case 'table':
                $this->tableList();
                break;
            default:
                $this->tableList();
        }
    }


    private function treeList()
    {
        $this->createTreeClass();
        $this->createTreeItemView();
        $this->createTreeView();

    }

    private function tableList()
    {
        $this->createClass();
        $this->createView();
    }

    private function createTreeClass(){
        $pathSave = $this->getClassFile("Index.php");
        $fields = $this->generateListFields();
        return $this->createFile(
            path: $pathSave,
            name: 'Index.php',
            data: [
                "DUMP_MY_ARR_FIELDS" => implode(',' . $this->showNewLine(4), $fields)
            ],
            stub: 'Tree.php.stub'
        );
    }
    private function createTreeView(){
        $pathSave = $this->getViewFile("index.blade.php");
        $labels = $this->generateTableLabel();
        $items = $this->generateTableItem();
        return $this->createFile(
            path: $pathSave
            , name: "index.blade.php"
            , data: [
            'DUMP_MY_TABLE_LABELS' => implode($this->showNewLine(5), $labels),
            'DUMP_MY_TABLE_ITEMS' => implode($this->showNewLine(4), $items)
        ]
            , stub: 'tree-listing.blade.php.stub'
            , force: true
        );
    }

    private function createTreeItemView(){
        $pathSave = $this->getViewFile("tree.blade.php");
        $labels = $this->generateTableLabel();
        $items = $this->generateTableTreeItem();
        return $this->createFile(
            path: $pathSave
            , name: "index.blade.php"
            , data: [
            'DUMP_MY_TABLE_LABELS' => implode($this->showNewLine(5), $labels),
            'DUMP_MY_TABLE_ITEMS' => implode($this->showNewLine(4), $items)
        ]
            , stub: 'tree-item.blade.php.stub'
            , force: true
        );
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

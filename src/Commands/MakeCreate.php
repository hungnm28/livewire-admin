<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\WithCommandTrait;
use Illuminate\Console\Command;

class MakeCreate extends Command
{
    use WithCommandTrait;
    protected $signature = 'la:make-create {name} {module} {--fileName=Create} {--force} {--model=}';

    protected $description = 'Make create Page ';

    public function handle()
    {
        $this->info("make Create file: " . $this->argument("name"));
        $this->initPath($this->argument("name"));
        $this->initModule($this->argument("module"));
        $this->initModel($this->argument("name"));
        $this->initFileName();
        $this->createClass();
        $this->createView();
        return true;
    }
    private function createClass()
    {
        $stub = $this->getStub("Create.php.stub");
        $template = $this->replaceStub($stub);
        $pathSave = $this->getClassFile("$this->fileName.php");
        $this->writeFile($pathSave, $template);

    }

    private function createView()
    {
        $stub = $this->getStub("create.blade.php.stub");
        $fields = "";
        foreach ($this->getModelFields() as $f => $field) {
            if(in_array($f,$this->reservedColumn)) continue;
            switch ($field->type) {
                case "boolean":
                    $form = '<x-lf.form.toggle name="' . $f . '" class="md:w-1/2" label="' . $field->label . '" />';
                    break;
                case "text":
                case "long-text":
                    $form = '<x-lf.form.textarea name="' . $f . '" label="' . $field->label . '" placeholder="' . $field->label . ' ..." />';
                    break;
                case "json":
                    $form = '<x-lf.form.array name="' . $f . '" class="md:w-1/2" label="' . $field->label . '" placeholder="' . $field->label . ' ..." :params="$' . $f . '"/>';
                    break;
                default:
                    $form = '<x-lf.form.input name="' . $f . '" class="md:w-1/2" type="' . $field->type . '" label="' . $field->label . '" placeholder="' . $field->label . ' ..."/>';
            }
            $fields .= $form . $this->showNewLine(4);
        }
        $template = str_replace([
            'DumpMyFields'
        ],
            [
                $fields
            ],
            $stub);
        $template = $this->replaceStub($template);
        $pathSave = $this->getViewFile($this->getSnakeString($this->fileName).".blade.php");
        $this->writeFile($pathSave, $template);
    }

}

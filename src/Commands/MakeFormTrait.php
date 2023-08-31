<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\WithCommandTrait;
use Illuminate\Console\Command;

class MakeFormTrait extends Command
{
    use WithCommandTrait;

    protected $signature = 'la:make-form-trait {name} {module} {--fileName=FormTrait} {--model=} {--force}';

    protected $description = 'Make form trait';
    public function handle()
    {
        $this->info("make Create file: " . $this->argument("name"));
        $this->initPath($this->argument("name"));
        $this->initModule($this->argument("module"));
        $this->initModel($this->argument("name"));
        $this->initFileName();
        $this->createFile();
    }

    private function createFile(){
        $listField = '';
        $rules = '';
        $createFields = '';
        foreach ($this->getModelFields() as $f => $field) {
            if (!in_array($f, $this->reservedColumn)) {
                $default = $field->default;
                switch ($field->type){
                    case "json": $default = '[]';
                        break;
                    default:
                        if($default){
                            $default = "'$default'";
                        }
                }
                if ($default) {
                    $listField .= '$' . $f . "= $default, ";
                } else {
                    $listField .= '$' . $f . ", ";
                }
                $rules .= "'$f' => '$field->rule'," . $this->showNewLine(4);
                $createFields .= "'$f' => \$this->$f," . $this->showNewLine(5);
            }
        }
        $listField = trim($listField, ', ');
        if($listField ==""){
            $listField = '$recode_id';
        }
        $stub = $this->getStub("FormTrait.stub");
        $template = str_replace([
            'DumpMyListFields'
            , 'DumpMyRules'
            , 'DumpMyFormFields'
        ], [
            $listField
            , $rules
            , $createFields
        ], $stub);
        $template = $this->replaceStub($template);
        $pathSave = $this->getClassFile("$this->fileName.php");
        $this->writeFile($pathSave, $template);
    }

}

<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Supports\ModelSupport;
use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;

class ModelCommand extends Command
{
    protected $signature = 'la:model {model} {--force}';
    protected $description = 'Generate Model class ';

    use CommandTrait;

    public function handle()
    {
        $this->alert("Generate Model: " . $this->argument("model"));
        $this->info("Run migrate");
        $this->call('migrate');
        $this->init();
        $this->backupModel();
        $this->generateModel();
    }

    private function generateModel()
    {
        if($this->argument("model") == "User"){
            $stub = $this->getStub("User.php.stub");
        }else{
            $stub = $this->getStub("Model.stub");
        }

        if (!$stub) {
            return false;
        }
        if (!$this->model) {
            $this->error("Invalid model [" . $this->argument("model") . "]!");
            return false;
        }
        $modelSupport = new ModelSupport($this->model);
        $stub = str_replace([
            "DUMP_MY_MODEL_TABLE"
            , "DUMP_MY_MODEL_NAME"
            , "DUMP_MY_MODEL_FIELDS"
            , "DUMP_MY_MODEL_LIST_FIELDS"
            , "DUMP_MY_MODEL_CAST_CLASSES"
            , "DUMP_MY_MODEL_CASTS"
            , "DUMP_MY_MODEL_RELATIONSHIP"
        ], [
            $this->model->getTable()
            , class_basename($this->model)
            , json_encode(array_keys($modelSupport->getFillables()))
            , json_encode(array_keys($modelSupport->getListFields()))
            , implode("", $modelSupport->getUseCasts())
            , implode(", " . $this->showNewLine(5), $modelSupport->getCasts())
            , $modelSupport->getRelationships()
        ], $stub);

        $name = class_basename($this->model);

        return $this->writeFile(app_path("Models/$name.php"), $stub);
    }

    private function backupModel()
    {
        $name = class_basename($this->model);
        $path = app_path("Models/$name.php");
        // Backup model
        $pathBackup = app_path("Models/backups/" . $name . date("_Y_m_d_", time()) . time() . ".backup");
        $this->line("Backup model: $name");
        if (file_exists($path)) {
            $this->writeFile($pathBackup, file_get_contents($path));

        } else {
            $this->error("Invalid model [$name]");
        }
    }


}
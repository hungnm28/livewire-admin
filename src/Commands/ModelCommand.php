<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Supports\ModelGenerator;
use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ModelCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:model {name}';

    protected $description = 'Generate Model class ';

    protected $castType = [
        "json" => "JsonCast"
        , "array" => "JsonCast"
        , "object" => "JsonCast"
        , "string" => "StringCast"
        , "text" => "TextCast"
        , "email" => "EmailCast"
        , "boolean" => "BooleanCast"
        , "integer" => "IntegerCast"
        , "tinyinteger" => "IntegerCast"
        , "bigint" => "BigintCast"
    ];

    protected $castName = [
        "email" => "EmailCast"
        , "slug" => "SlugCast"
    ];

    public function handle()
    {
        $this->info("Generate Model: " . $this->argument("name"));
        $this->info("Run migrate");
        $this->call('migrate');
        $this->backupModel();
        $this->generateModel();
    }

    private function generateModel()
    {
        $name = $this->getModelName();
        $modelGenerator = new ModelGenerator($name);
        $fields = $modelGenerator->getFields();
        $fieldNames = $this->getFieldNames($fields);
        $listFields = $this->getFieldNames($fields,1);
        $casts = $this->getCasts($fields);
        $tableName = $modelGenerator->getTableName();
        $relationship = '';
        if(in_array("\"parent_id\"",$fieldNames)){
            $relationship = $this->generateRelationship($name);
        }
        $stub = $this->getStub("Model.stub");
        $template = str_replace([
            "DUMP_MY_CAST_CLASSES",
            "DUMP_MY_CLASS_NAME",
            "DUMP_MY_TABLE",
            "DUMP_MY_FIELDS",
            "DUMP_MY_LIST_FIELDS",
            "DUMP_MY_RELATIONSHIP",
            "DUMP_MY_CASTS",
        ],[
            implode("",$casts['castClasses']),
            $name,
            $tableName,
            implode(", ",$fieldNames),
            implode(", ",$listFields),
            $relationship,
            implode(", ".$this->showNewLine(4),$casts['casts'])

        ],$stub);
        $pathSave = app_path("Models/$name.php");
        // Backup model
        $pathBackup = app_path("Models/backups/" . $name . date("_Y_m_d_", time()).time() . ".backup");
        $this->info("Backup old model to $pathBackup");
        if(file_exists($pathSave)){
            $this->writeFile($pathBackup, file_get_contents($pathSave));
        }
        $this->writeFile($pathSave, $template,true);
    }

    private function getCasts($fields)
    {
        $casts = [];
        $castClasses = [];
        foreach ($fields as $field=> $item) {
            if (isset($this->castType[$item->type])) {
                $castName = $this->castType[$item->type];
                if ($this->checkCastExits($castName)) {
                    $castClasses[$castName] = "use App\Casts\\$castName; \n";
                    $casts[$field]= "\"$field\" => $castName::class";
                }
            }
        }
        return [
            'casts' => $casts,
            "castClasses" => $castClasses
        ];
    }

    private function generateRelationship($name){
      return 'public function children(){
        return $this->hasMany('.$name.'::class,"parent_id","id");
    }

    public function parent(){
        return $this->belongsTo('.$name.'::class,"parent_id","id");
    }';
    }


    private function getFieldNames($fields,$full=0)
    {
        $rt = [];
        foreach ($fields as $field => $val) {
            if(!$this->checkIgnoreField($field)){
                if (!$this->checkReservedField($field) || $full) {
                    $rt[] = "\"$field\"";
                }
            }

        }
        return $rt;
    }

    private function getModelName()
    {
        $name = $this->argument("name");
        $name = Str::singular($name);
        return $name;
    }

    private function getModelPath($name)
    {
        return app_path("Models/$name.php");
    }

    private function backupModel()
    {
        $name = $this->getModelName();
        $path = $this->getModelPath($name);
    }
}

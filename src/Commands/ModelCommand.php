<?php

namespace Hungnm28\LivewireAdmin\Commands;


use Hungnm28\LivewireAdmin\Traits\WithCommandTrait;
use Illuminate\Console\Command;

class ModelCommand extends Command
{
    use WithCommandTrait;

    protected $signature = 'la:model {name} {--model=} {--force}';

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
        $this->initModel($this->argument("name"));
        $this->createModel();
    }

    protected function createModel()
    {
        $fields = data_get($this->model,"fields",[]);
        $dumpFields = "";
        $dumpCastClass = [];
        $dumpCasts = "";
        $dumpListFields = "";
        foreach ($fields as $f => $row) {
            if (!in_array($f,$this->reservedColumn)) {
                $dumpFields .= "\"$f\", ";
            }
            $dumpListFields .= ", \"$f\"";
            $cast = data_get($this->castName, $f, data_get($this->castType, $row->type));
            if ($cast && $this->checkCastExits($cast)) {
                $dumpCastClass[$cast] = "use App\Casts\\$cast;";
                $dumpCasts .= "\"$f\" => $cast::class,\r\n\t\t";
            }//if ($cast)
        }//foreach ($fields as $f => $row)
        $dumpFields = trim($dumpFields, ", ");
        $dumpListFields = trim($dumpListFields, ", ");
        $dumpCasts = trim($dumpCasts, ",");

        $stub = $this->getStub("model.stub");

        $template = str_replace(
            [
                'DumpMyCastsClass',
                'DumpMyClassName',
                'DumpMyTable',
                'DumpMyFillable',
                'DumpMyListFields',
                'DumpMyCasts'
            ],
            [
                implode("\n", $dumpCastClass),
                data_get($this->model,"name"),
                data_get($this->model,"table"),
                $dumpFields,
                $dumpListFields,
                $dumpCasts
            ],
            $stub
        );
        $pathSave = app_path("Models/" . $this->argument("name") . ".php");
        // Backup model
        $pathBackup = app_path("Models/backups/" . $this->argument("name") . date("_Y_m_d_", time()).time() . ".backup");
        $this->info("Backup old model to $pathBackup");
        $this->writeFile($pathBackup, file_get_contents($pathSave));
        $this->writeFile($pathSave, $template);
    }

    protected function checkCastExits($name)
    {
        return class_exists("App\Casts\\$name");
    }
}

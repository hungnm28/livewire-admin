<?php

namespace Hungnm28\LivewireAdmin\Supports;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ModelSupport
{
    private $model, $name, $status = 0, $columns = [], $message = "Invalid Model";
    protected $fieldRulerMapping = [
        "name" => "string"
        , "email" => "email"
        , "password" => "required|min:8"
        , "url" => "url"
        , "slug" => "required|string"
    ];
    protected $ignoreFillables = [
        'created_at', 'updated_at', 'deleted_at', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'
    ];
    protected $ignoreLists = ['remember_token', 'two_factor_recovery_codes', 'two_factor_secret', 'password'];
    protected $hiddenLists = ['slug', 'created_at', 'updated_at', 'deleted_at', 'email_verified_at', 'two_factor_confirmed_at'];
    protected $ignoreTypeLists = ['text', 'longtext', 'json', 'array'];

    protected $castType = [
        "json" => "JsonCast"
        , "array" => "JsonCast"
        , "object" => "JsonCast"
        , "string" => "StringCast"
        , "varchar" => "StringCast"
        , "text" => "TextCast"
        , "longtext" => "TextCast"
        , "email" => "EmailCast"
        , "boolean" => "BooleanCast"
        , "integer" => "IntegerCast"
        , "tinyint" => "IntegerCast"
        , "bigint" => "BigintCast"
    ];

    protected $castName = [
        "email" => "EmailCast"
        , "slug" => "SlugCast"
    ];

    protected $castBase = [
        "datetime"=>"datetime"
        ,"date"=>"date"
        ,"timestamp"=>"timestamp"
        ,"boolean"=>"boolean"
        ,"tinyint"=>"boolean"
        ,"integer"=>"integer"
        ,"double"=>"double"
        ,"float"=>"float"
        ,"collection"=>"collection"
    ];

    protected $castBaseName = [
        "password"=>"hashed"
    ];

    public function __construct($name)
    {
        if ($name instanceof Model) {
            $this->model = $name;
            $this->status = 1;
            $this->name = class_basename($this->model->getMorphClass());
        }
        $nameSpace = "App\\Models\\$name";
        $this->message = "Invalid model [$name] !";
        if (class_exists($nameSpace) && is_subclass_of($nameSpace, Model::class)) {
            $model = new $nameSpace();
            if ($model) {
                $this->model = $model;
                $this->message = "";
                $this->status = 1;
                $this->name = $name;
            }
        }
    }

    public function getColumns()
    {
        if ($this->status != 1) {
            return [];
        }
        $columns = Schema::getColumns($this->getTable());
        foreach ($columns as $column) {
            $name = $column["name"];
            $ruler = data_get($this->fieldRulerMapping, $name);
            $column["rule"] = $ruler;
            switch ($column["type_name"]) {
                case "tinyint":
                case "integer":
                case "boolean":
                case "bigint":
                case "int":
                    $column["default"] = intval($column["default"]);
                    break;
                case "json":
                case "array":
                    $column["default"] = '[]';
                    break;
                default:
                    if ($column["default"] !== null) {
                        $column["default"] = "'".trim($column["default"],"\n\r\t\v\0\"'")."'";
                    }
            }
            $data[$name] = $column;
        }
        $this->columns = $data;
        return $data;
    }

    public function getListFields()
    {
        $return = [];
        if ($this->status != 1) {
            return $return;
        }
        if (empty($this->columns)) {
            $this->getColumns();
        }
        foreach ($this->columns as $name => $column) {
            if (in_array($name, $this->ignoreLists) || in_array($column['type_name'], $this->ignoreTypeLists)) {
                continue;
            }
            $column["hidden"] = in_array($name,$this->hiddenLists);
            $return[$name] = $column;
        }
        return $return;
    }

    public function getFillables()
    {
        $return = [];
        if ($this->status != 1) {
            return $return;
        }
        if (empty($this->columns)) {
            $this->getColumns();
        }
        foreach ($this->columns as $name => $column) {
            if (in_array($name, $this->ignoreFillables) || $name == $this->model->getKeyName()) {
                continue;
            }

            $return[$name] = $column;
        }
        return $return;
    }


    public function getUseCasts()
    {
        $return = [];
        if ($this->status == 0) {
            return $return;
        }
        $fillables = $this->getFillables();
        foreach ($fillables as $name => $item) {
            if (isset($this->castName[$name])) {
                if ($this->checkCastExits($this->castName[$name])) {
                    $return[$this->castName[$name]] = "use App\\Casts\\" . $this->castName[$name] . ";";
                }
            } else {

                if (isset($this->castType[$item["type_name"]])) {
                    $cast = $this->castType[$item["type_name"]];
                    if ($this->checkCastExits($cast)) {
                        $return[$cast] = "use App\\Casts\\$cast;";
                    }
                }
            }
        }// foreach ($fillables as $name =>$item){
        return $return;
    }


    public function getCasts()
    {

        $return = [];
        if ($this->status == 0) {
            return $return;
        }
        $fillables = $this->getFillables();
        foreach ($fillables as $name => $item) {
            if(isset($this->castBaseName[$name])){
                $return[$name] = "\"$name\" => \"" . $this->castBaseName[$name] . "\"";
                continue;
            }
            if (isset($this->castBase[$item["type_name"]])) {
                $return[$name] = "\"$name\" => \"" . $this->castBase[$item["type_name"]] . "\"";
                continue;
            }
            if (isset($this->castName[$name])) {
                if ($this->checkCastExits($this->castName[$name])) {
                    $return[$name] = "\"$name\" => " . $this->castName[$name] . "::class";
                }
            } else {
                if (isset($this->castType[$item["type_name"]])) {
                    $cast = $this->castType[$item["type_name"]];
                    if ($this->checkCastExits($cast)) {
                        $return[$name] = "\"$name\" => $cast::class";
                    }
                }
            }
        }// foreach ($fillables as $name =>$item){
        return $return;
    }

    public function getRelationships()
    {
        if ($this->status != 1) {
            return null;
        }
        if (empty($this->columns)) {
            $this->getColumns();
        }

        if (isset($this->columns["parent_id"])) {


            return 'public function children()
    {
        return $this->hasMany(' . $this->name . '::class,"parent_id","id");
    }

    public function parent()
    {
        return $this->belongsTo(' . $this->name . '::class,"parent_id","id");
    }';
        }
        return null;

    }

    protected function checkCastExits($name)
    {
        return class_exists("App\\Casts\\$name");
    }

    public function getTable()
    {
        $return = "";
        if ($this->model) {
            $return = $this->model->getConnection()->getTablePrefix() . $this->model->getTable();
        }
        return $return;
    }

}
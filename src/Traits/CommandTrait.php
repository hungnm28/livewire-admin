<?php

namespace Hungnm28\LivewireAdmin\Traits;

use Hungnm28\LivewireAdmin\Supports\ModelGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use function Laravel\Prompts\confirm;

trait CommandTrait
{
    private $module, $model, $path, $pageName, $fileName;
    private $reservedColumn = [
        'id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'
    ];

    private function initModule($name)
    {
        $this->module = Module::findOrFail($name);
    }

    private function initPath($path)
    {
        $name = Str::afterLast($path, "/");
        $pre = Str::beforeLast($path, $name);
        $name = Str::plural($name);
        $name = Str::studly($name);
        $this->path = $pre . $name;

    }

    private function initModel($pageName)
    {
        $modelName = null;

        if ($this->hasOption("model") && $this->option("model")) {
            $modelName = $this->option("model");
        }
        if (!$modelName) {
            $modelName = Str::afterLast($pageName, "/");
            $modelName = Str::singular($modelName);

        }
        $modelGenerator = new ModelGenerator($modelName);
        $this->model = [
            "name" => $modelName
            , "table" => $modelGenerator->model->getTable()
            , "fields" => $modelGenerator->getFields()
        ];

    }

    private function generateListFields()
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $item) {
            $rt[$item->name] = '"'.$item->name.'" => ["status" => true, "label" => "' . $item->label . '"]';
        }
        return $rt;
    }

    private function generateTableLabel()
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $item) {
            $rt[$item->name] = '<x-lf.table.label :$fields name="' . $item->name . '">' . $item->label . '</x-lf.table.label>';
        }
        return $rt;
    }

    private function generateTableItem()
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $item) {
            switch ($item->type) {
                case "json":
                    $rt[$item->name] = '<x-lf.table.item :$fields name="' . $item->name . '">JSON FIELD</x-lf.table.item>';
                    break;
                default:
                    $rt[$item->name] = '<x-lf.table.item :$fields name="' . $item->name . '">{{$item->' . $item->name . '}}</x-lf.table.item>';
            }
        }
        return $rt;
    }


    private function generateShowFields()
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $item) {
            switch ($item->type) {
                case "json":
                    $rt[$item->name] = '<tr>
            <th class="stt">' . $item->label . ':</th>
            <td></td>
        </tr>';
                    break;
                default:
                    $rt[$item->name] = '<tr>
            <th class="stt">' . $item->label . ':</th>
            <td>{{$data->' . $item->name . '}}</td>
        </tr>';
            }
        }
        return $rt;
    }

    private function generateForm()
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $field => $item) {
            if (!$this->checkReservedField($field)) {
                switch ($item->type) {
                    case "text":
                    case "long-text":
                        $rt[$field] = '<lf.form.textarea name="' . $item->name . '" class="form-input" label="' . $item->label . '" placeholder="' . $item->label . ' ..."/>';
                        break;
                    case "boolean":
                        $rt[$field] = '<lf.form.checkbox name="' . $item->name . '" class="form-input" label="' . $item->label . '"/>';
                        break;
                    case "json":
                        $rt[$field] = '<lf.form.array name="' . $item->name . '" class="form-input" label="' . $item->label . '"/>';
                        break;
                    default:
                        $rt[$field] = '<lf.form.input type="' . $item->type . '" name="' . $item->name . '" class="form-input" label="' . $item->label . '" placeholder="' . $item->label . ' ..."/>';
                        break;
                }
            }
        }

        return $rt;

    }

    private function replaceStub($stub)
    {
        return str_replace([
            "DUMP_MY_MODULE_NAME",
            "DUMP_MY_NAMESPACE",
            "DUMP_MY_MODEL_NAME",
            "DUMP_MY_MODULE_SLUG",
            "DUMP_MY_MODULE_HEAD_NAME",
            "DUMP_MY_MODULE_LOWER_NAME",
            "DUMP_MY_DOT_PAGE",
            "DUMP_MY_PAGE_NAME",
            "DUMP_MY_PAGE_LOWER_NAME",
            "DUMP_MY_CREATE_PERMISSION",
            "DUMP_MY_EDIT_PERMISSION",
            "DUMP_MY_SHOW_PERMISSION",
            "DUMP_MY_DELETE_PERMISSION",
            "DUMP_MY_PERMISSION",
            "DUMP_MY_SMALL_LOGO",
        ], [
            $this->getModuleName(),
            $this->getNamespace(),
            $this->getModelName(),
            $this->getModuleSug(),
            $this->getModuleHeadName(),
            $this->getModuleLowerName(),
            $this->getDotPath(),
            $this->getHeadline($this->path),
            $this->getPageLowerName(),
            $this->getPermissionName("create"),
            $this->getPermissionName("edit"),
            $this->getPermissionName("show"),
            $this->getPermissionName("delete"),
            $this->getPermissionName(),
            $this->getLogoText()

        ], $stub);

    }

    private function getModelName()
    {
        $name = data_get($this->model, "name");
        $name = explode("\\", $name);
        $name = @end($name);
        return $name;
    }

    private function getStub($file)
    {
        if ($this->isForce()) {
            $path = __DIR__ . "/../Commands/stubs/$file";
        } else {
            $path = base_path("stubs/livewire-form-stubs/$file");
            if (!File::exists($path)) {
                $path = __DIR__ . "/../Stubs/$file";
            }
        }
        if (!File::exists($path)) {
            $this->error("WHOOPS-IE-TOOTLES  😳 \n");
            $this->error("Stubs not exists: $path \n ");
            return false;
        }
        return file_get_contents($path);
    }

    private function checkReservedField($name)
    {
        return in_array($name, $this->reservedColumn);
    }

    private function ensureDirectoryExists($path)
    {
        $path = dirname($path);
        (new Filesystem)->ensureDirectoryExists($path);
    }

    private function getSnakeString($str)
    {
        $str = Str::snake($str, "-");
        return Str::slug($str);
    }

    private function getHeadline($str = '')
    {
        $str = Str::replace("/", " ", $str);
        $str = Str::snake($str, "-");
        return Str::headline($str);
    }

    private function getPageLowerName(){
        return Str::lower($this->path);
    }
    private function isForce()
    {
        if ($this->hasOption("force")) {
            return $this->option('force') === true;
        }
        return false;

    }

    private function getModuleName()
    {
        return $this->module->getName();
    }

    private function getModuleLowerName()
    {
        return $this->module->getLowerName();
    }

    private function getModuleHeadName()
    {
        return $this->getHeadline($this->module->getName());
    }

    private function getModuleSug()
    {
        return $this->getSnakeString($this->module->getName());
    }

    private function showNewLine($t = 0)
    {
        return Str::padRight("\r\n", $t, "\t");
    }

    protected function checkCastExits($name)
    {
        return class_exists("App\Casts\\$name");
    }

    private function createFile($path, $name, $data = [], $stub = "", $force = false)
    {
        if ($stub == "") {
            $stub = $name . ".stub";
        }
        $template = $this->getStub($stub);
        if (!$template) {
            return false;
        }
        $template = $this->replaceStub($template);
        if (!empty($data)) {
            $template = str_replace(array_keys($data), array_values($data), $template);
        }
        return $this->writeFile($path, $template, $force);
    }

    private function writeFile($path, $data, $force = false)
    {
        if (file_exists($path) && !$force) {
            $confirmed = confirm(
                label: 'This file exits, do you want replace it?',
                hint: 'file exits: ' . $path
            );
            if (!$confirmed) {
                return false;
            }
        }
        $this->ensureDirectoryExists($path);
        $this->info("create file: $path");
        return File::put($path, $data);
    }

    private function getNamespace()
    {
        $namespace = "Modules/" . $this->module->getName() . "/Livewire/" . $this->path;
        $namespace = str_replace("/", "\\", $namespace);
        return $namespace;
    }

    private function getFields()
    {
        return data_get($this->model, "fields", []);
    }

    private function getClassFile($name)
    {
        return $this->getModulepath("Livewire/$this->path/$name");
    }

    private function getViewFile($name)
    {
        $data = [];
        foreach (explode("/", $this->path) as $path) {
            $data[] = $this->getSnakeString($path);
        }
        $path = implode("/", $data);
        return $this->getModulepath("Resources/views/livewire/$path/$name");
    }

    private function getModulepath($path = "")
    {
        return $this->module->getPath() . "/$path";
    }

    private function getPermissionName($name = "")
    {
        $permission = $this->getModuleSug() . "." . $this->getDotPath();
        if ($name != "") {
            $permission = $permission . "." . $name;
        }
        return $permission;
    }

    private function getLogoText(){
        return Str::upper(Str::substr($this->getModuleHeadName(),0,3));
    }

    private function getDotPath()
    {
        $data = [];
        foreach (explode("/", $this->path) as $name) {
            $data[] = $this->getSnakeString($name);
        }
        return implode(".", $data);
    }
}

<?php

namespace Hungnm28\LivewireAdmin\Traits;

use Hungnm28\LivewireAdmin\Supports\ModelGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

trait WithCommandTrait
{
    private $module, $model, $path, $fileName;

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

    private function initFileName()
    {
        $fileName = $this->option("fileName");
        $fileName = Str::before($fileName, '.php');
        $this->fileName = $fileName;
    }

    private function initModel($path)
    {
        $modelName = null;

        if ($this->option("model")) {
            $modelName = $this->option("model");
        }

        if (!$modelName) {
            $modelName = Str::afterLast($path, "/");
            $modelName = Str::singular($modelName);

        }
        $modelGenerator = new ModelGenerator($modelName);
        $this->model = [
            "name" => $modelName
            , "table" => $modelGenerator->model->getTable()
            , "fields" => $modelGenerator->getFields()
        ];

    }


    private function getNamespace()
    {
        $namespace = "Modules/" . $this->module->getName() . "/Http/Livewire/" . $this->path;
        $namespace = str_replace("/", "\\", $namespace);
        return $namespace;
    }

    private function getModulepath($path = "")
    {
        return $this->module->getPath() . "/$path";
    }

    private function getDots()
    {
        $data = [];
        foreach (explode("/", $this->path) as $name) {
            $data[] = $this->getSnakeString($name);
        }
        return implode(".", $data);
    }

    private function getClassFile($name)
    {
        return $this->getModulepath("Http/Livewire/$this->path/$name");
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

    private function getPageName()
    {
        $name = Str::afterLast($this->path, "/");
        return $this->getHeadline($name);
    }

    private function getRouteName($name = "")
    {
        $str = $this->getModuleSug();
        foreach (explode("/", $this->path) as $path) {

            if ($this->getSnakeString($path) != "") {
                $str .= "." . $this->getSnakeString($path);
            }
        }
        if ($name) {
            $str .= ".$name";
        }
        return $str;
    }

    private function getPermissionName($name = "")
    {
        $str = $this->getModuleSug();
        foreach (explode("/", $this->path) as $path) {
            $str .= "." . $this->getSnakeString($path);
        }
        if ($name) {
            $str .= ".$name";
        }
        return $str;
    }

    private function getModelNamspace()
    {
        if (class_exists($this->model["name"])) {
            return $this->model["name"];
        }
        return "App\\Models\\" . $this->model["name"];
    }

    private function getArrRoutes($name = "")
    {
        $return = [];
        $route = $this->getModuleSug();
        $return[$route] = $this->getModuleHeadName();
        foreach (explode("/", $this->path) as $path) {
            $route .= '.' . $this->getSnakeString($path);
            $return[$route] = $this->getHeadline($path);
        }
        if ($name) {
            $route .= ".$name";
            $return[$route] = $this->getHeadline($name);
        }

        return $return;
    }

    private function getStub($file)
    {
        if ($this->isForce()) {
            $path = __DIR__ . "/../Stubs/$file";
        } else {
            $path = base_path('stubs/livewire-admin-stubs/' . $file);
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

    private function replaceStub($str)
    {
        return str_replace([

            "DumpMyPageName"
            , "DumpMyNamespace"
            , 'DumpMyClassName'
            , 'DumpMyFileSlug'
            , "DumpMyModuleName"
            , "DumpMyModuleLowerName"
            , "DumpMyModuleHeadName"
            , "DumpMyModuleSlug"
            , "DumpMyModelNamespace"
            , "DumpMyModelClassName"
            , "DumpMyPermission"
            , "DumpMyRoute"
            , "DumpMyView"
            , "DumpMyAssets"
        ], [
            $this->getPageName()
            , $this->getNamespace()
            , $this->fileName
            , $this->getSnakeString($this->fileName)
            , $this->getModuleName()
            , $this->getModuleLowerName()
            , $this->getModuleHeadName()
            , $this->getModuleSug()
            , $this->getModelNamspace()
            , $this->getModelName()
            , $this->getPermissionName()
            , $this->getRouteName()
            , $this->getDots()
            , $this->getModuleSug()
        ], $str);
    }

    private function replaceModuleStub($str)
    {
        return str_replace([
            "DumpMyModuleName"
            , "DumpMyModuleLowerName"
            , "DumpMyModuleHeadName"
            , "DumpMyModuleSlug"
        ], [
            $this->getModuleName()
            , $this->getModuleLowerName()
            , $this->getModuleHeadName()
            , $this->getModuleSug()
        ], $str);

    }

    private function writeFile($path, $data)
    {
        $this->ensureDirectoryExists($path);
        return File::put($path, $data);
    }

    private function checkModule($name)
    {
        return Module::has($name);
    }

    private function getModelName()
    {
        $name = data_get($this->model, "name");
        $name = explode("\\", $name);
        $name = @end($name);
        return $name;
    }

    private function getTableName()
    {
        return data_get($this->model, "table");
    }

    private function getModelFields()
    {
        return data_get($this->model, "fields", []);
    }

    private function isForce()
    {
        return $this->option('force') === true;
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
}

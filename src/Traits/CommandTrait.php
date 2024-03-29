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
    private $module, $model, $path, $pageName, $fileName, $paths;
    private $reservedColumn = [
        'id', 'created_at', 'updated_at', 'deleted_at'
    ];
    private $ignoreColumn = ['remember_token', 'two_factor_recovery_codes', 'two_factor_secret'];

    public function __construct()
    {
        parent::__construct();
        $this->setPaths();
    }

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

    private function setPaths()
    {
        $paths = [
            'assets' => config("modules.paths.generator.assets.path"),
            'views' => config("modules.paths.generator.views.path"),
            'routes' => config("modules.paths.generator.routes.path"),
            'controller' => config("modules.paths.generator.controller.path"),
            'provider' => config("modules.paths.generator.provider.path"),
            'componentView' => config("modules.paths.generator.component-view.path"),
            'componentClass' => config("modules.paths.generator.component-class.path"),
            'livewireClass' => config("modules-livewire.namespace"),
            'livewireView' => config("modules-livewire.view"),
        ];

        $this->paths = (object)$paths;
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
            $status = 'true';
            switch ($item->name) {
                case 'created_at':
                case 'updated_at':
                    $status = 'false';
            }
            if ($item->name != "id") {
                $rt[$item->name] = '"' . $item->name . '" => ["status" => ' . $status . ', "label" => "' . $item->label . '"]';
            }
        }
        return $rt;
    }

    private function generateTableLabel()
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $item) {
            if ($item->name != "id") {
                $rt[$item->name] = '<x-lf.table.label :$fields name="' . $item->name . '">' . $item->label . '</x-lf.table.label>';

            }
        }
        return $rt;
    }

    private function generateTableItem()
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $item) {
            if ($item->name != "id") {
                switch ($item->type) {
                    case "json":
                        $rt[$item->name] = '<x-lf.table.item :$fields name="' . $item->name . '">JSON FIELD</x-lf.table.item>';
                        break;
                    default:
                        $rt[$item->name] = '<x-lf.table.item :$fields name="' . $item->name . '">{{$item->' . $item->name . '}}</x-lf.table.item>';
                }
            }

        }
        return $rt;
    }

    private function generateTableTreeItem()
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $item) {
            if ($item->name != "id") {
                switch ($item->type) {
                    case "json":
                        $rt[$item->name] = '<x-lf.table.item :fields="$this->fields" name="' . $item->name . '">JSON FIELD</x-lf.table.item>';
                        break;
                    default:
                        $rt[$item->name] = '<x-lf.table.item :fields="$this->fields" name="' . $item->name . '">{{$item->' . $item->name . '}}</x-lf.table.item>';
                }
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

    private function generateForm($page = null)
    {
        $fields = $this->getFields();
        $rt = [];
        foreach ($fields as $field => $item) {
            if (!$this->checkReservedField($field)) {
                $id = $this->randomChars(3, $field);
                switch ($item->type) {
                    case "text":
                    case "long-text":
                        $rt[$field] = '<x-lf.form.textarea name="' . $item->name . '" label="' . $item->label . '"  placeholder="' . $item->label . ' ..." id="' . $id . '"/>';
                        break;
                    case "boolean":
                        $rt[$field] = '<x-lf.form.toggle name="' . $item->name . '"  label="' . $item->label . '" :checked="$' . $item->name . ' == 1"  id="' . $id . '" />';
                        break;
                    case "json":
                        $rt[$field] = '<x-lf.form.json type="' . $item->type . '" name="' . $item->name . '" label="' . $item->label . '" :data="$' . $item->name . '" id="' . $id . '"/>';
                        break;
                    default:
                        $rt[$field] = '<x-lf.form.input type="' . $item->type . '" name="' . $item->name . '" label="' . $item->label . '" placeholder="' . $item->label . ' ..." id="' . $id . '" />';
                        break;
                }
            }
        }

        return $rt;

    }

    private function randomChars($number = 1, $pre = '')
    {
        if ($pre != "") {
            return $pre . "-" . substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", $number)), 0, $number);
        }
        return substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", $number)), 0, $number);
    }

    private function replaceStub($stub)
    {
        return str_replace([
            "DUMP_MY_MODULE_NAME",
            "DUMP_MY_NAMESPACE",
            "DUMP_MY_LIVEWIRE_NAMESPACE",
            "DUMP_MY_MODEL_NAME",
            "DUMP_MY_MODULE_SLUG",
            "DUMP_MY_MODULE_HEAD_NAME",
            "DUMP_MY_MODULE_LOWER_NAME",
            "DUMP_MY_DOT_PAGE",
            "DUMP_MY_PAGE_NAME",
            "DUMP_MY_PAGE_SLUG_NAME",
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
            str_replace("/", "\\", $this->paths->livewireClass),
            $this->getModelName(),
            $this->getModuleSug(),
            $this->getModuleHeadName(),
            $this->getModuleLowerName(),
            $this->getDotPath(),
            $this->getHeadline($this->path),
            $this->getPageSlug(),
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

    private function checkIgnoreField($name)
    {
        return in_array($name, $this->ignoreColumn);
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

    private function getPageSlug()
    {
        $name = Str::afterLast($this->path, ".");
        return $this->getSnakeString($name);
    }

    private function getPageLowerName()
    {
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
        $namespace = "Modules/" . $this->module->getName() . "/" . $this->paths->livewireClass . "/" . $this->path;
        $namespace = str_replace("/", "\\", $namespace);
        return $namespace;
    }

    private function getFields()
    {
        return data_get($this->model, "fields", []);
    }

    private function getClassFile($name)
    {
        return $this->getModulepath($this->paths->livewireClass . "/$this->path/$name");
    }

    private function getViewFile($name)
    {
        $data = [];
        foreach (explode("/", $this->path) as $path) {
            $data[] = $this->getSnakeString($path);
        }
        $path = implode("/", $data);
        return $this->getModulepath($this->paths->livewireView . "/$path/$name");
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

    private function getLogoText()
    {
        return Str::upper(Str::substr($this->getModuleHeadName(), 0, 3));
    }

    private function getDotPath()
    {
        $data = [];
        foreach (explode("/", $this->path) as $name) {
            $data[] = $this->getSnakeString($name);
        }
        return implode(".", $data);
    }

    private function insertDataAfter($path, $flag, $check, $data)
    {
        if (!file_exists($path)) {
            $this->error("File not exits: $path");
            return false;
        }
        $template = file_get_contents($path);
        if (!Str::contains($template, $check)) {
            $template = str_replace($flag,
                $flag . $data
                , $template
            );
            return file_put_contents($path, $template);
        }
        return false;
    }
}

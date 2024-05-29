<?php

namespace Hungnm28\LivewireAdmin\Traits;

use Hungnm28\LivewireAdmin\Supports\ModelSupport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use function Laravel\Prompts\confirm;

trait CommandTrait
{
    private $model, $module, $folder;


    public function init()
    {

        $this->initFolder();
        $this->initModel();
        $this->initModule();

    }

    private function initFolder()
    {
        $name = "";
        $folder = "";
        if ($this->hasOption("folder")) {
            $name = $this->option("folder");
        }
        if (!$name && $this->hasArgument("folder")) {
            $name = $this->argument("folder");
        }
        if ($name) {
            $name = trim($name, "\/ ");
            $name = str_replace("\\", "/", $name);
            $arr = explode("/", $name);
            foreach ($arr as $k => $item) {
                if ($item != "") {
                    if ($k == count($arr) - 1) {
                        $item = Str::pluralStudly($item);
                    } else {
                        $item = Str::studly($item);
                    }
                    $folder .= "/$item";
                }
            }
        }
        $folder = trim($folder, "/");
        $this->folder = $folder;

    }


    private function initModule()
    {
        if ($this->hasOption("module")) {
            $this->module = Module::findOrFail($this->option("module"));
            return true;
        }
        if ($this->hasArgument("module")) {
            $this->module = Module::findOrFail($this->argument("module"));
            return true;
        }
        return false;
    }

    public function initModel()
    {

        if ($this->hasArgument("model")) {
            return $this->getModel($this->argument("model"));
        }

        if ($this->hasOption("model") && $this->option("model")) {
            return $this->getModel($this->option("model"));
        }

        if ($this->hasOption("folder")) {
            return $this->getModel($this->option("folder"));
        }
        if ($this->hasArgument("folder")) {
            return $this->getModel($this->argument("folder"));
        }
        return false;
    }

    private function getModel($name)
    {

        if (class_exists($name) && is_subclass_of($name, Model::class)) {
            $this->model = new $name();
            return true;
        }
        $name = trim($name, "/\ ,.\n\r\t\v\0");
        $name = Str::afterLast($name, "/");
        $name = Str::singular($name);
        $name = Str::studly($name);
        $namespace = "App\\Models\\$name";

        if (class_exists($namespace) && is_subclass_of($namespace, Model::class)) {
            $this->model = new $namespace();
            return true;
        }
        return false;
    }

    private function getTemplate($stubPath)
    {
        $stub = $this->getStub($stubPath);
        if (!$stub) {
            return "";
        }
        $stub = $this->replaceStub($stub);
        return $stub;
    }

    private function getViewTemplate($stubPath)
    {
        $stub = $this->getStub($stubPath);
        if (!$stub) {
            return "";
        }
        $stub = $this->replaceViewStub($stub);
        return $stub;
    }

    private function replaceViewStub($stub)
    {
        $stub = $this->replaceModule($stub);
        $stub = $this->replaceFolder($stub);
        $stub = $this->replaceViewModel($stub);
        return $stub;
    }

    private function replaceStub($stub)
    {
        $stub = $this->replaceModule($stub);
        $stub = $this->replaceFolder($stub);
        $stub = $this->replaceModel($stub);
        return $stub;
    }

    private function replaceViewModel($stub)
    {

        if (!$this->model) {
            return $stub;
        }
        $modelSupport = new ModelSupport($this->model);
        $fields = $modelSupport->getFillables();
        $listFields = $modelSupport->getListFields();
        return str_replace([
            "DUMP_MY_FORMS"
            , "DUMP_MY_SHOW_FIELDS"
            , "DUMP_MY_TABLE_LABELS"
            , "DUMP_MY_TABLE_ITEMS"
        ], [
            implode($this->showNewLine(3), array_values($this->generateForms($fields)))
            , implode($this->showNewLine(4), array_values($this->generateShows($fields)))
            , implode($this->showNewLine(4), array_values($this->generateTableLabels($listFields)))
            , implode($this->showNewLine(4), array_values($this->generateTableItems($listFields)))

        ], $stub);

    }

    private function replaceModel($stub)
    {
        if (!$this->model) {
            return $stub;
        }
        $modelSupport = new ModelSupport($this->model);
        $fields = $modelSupport->getFillables();
        $listFields = $modelSupport->getListFields();
        return str_replace([
            "DUMP_MY_MODEL_NAME"
            , "DUMP_MY_VARIABLES"
            , "DUMP_MY_RULES"
            , "DUMP_MY_SET_FIELDS"
            , "DUMP_MY_SET_DATA"
            , "DUMP_MY_ARR_FIELDS"
        ], [
            class_basename($this->model)
            , implode(", ", array_values($this->generateVariables($fields)))
            , implode($this->showNewLine(6, ","), array_values($this->generateRules($fields)))
            , implode($this->showNewLine(4), array_values($this->generateSetFields($fields)))
            , implode($this->showNewLine(6, ","), array_values($this->generateSetData($fields)))
            , implode($this->showNewLine(6, ","), array_values($this->generateArrFields($listFields)))

        ], $stub);
    }

    private function replaceFolder($stub)
    {
        if (!$this->folder) {
            return $stub;
        }
        return str_replace([
            "DUMP_MY_NAMESPACE"
            , "DUMP_MY_PAGE_NAME"
            ,"DUMP_MY_DOT_PAGE"
            ,"DUMP_MY_VIEW_FOLDER"
        ], [
            $this->getNamespace()
            , $this->getHeadLine($this->folder)
            ,$this->getDotFolder()
            ,$this->viewFolder()
        ], $stub);
    }

    private function replaceModule($stub)
    {
        if (!$this->module) {
            return $stub;
        }
        return str_replace([
            'DUMP_MY_MODULE_NAME'
            , 'DUMP_MY_MODULE_HEAD_NAME'
            , 'DUMP_MY_MODULE_SLUG'
            , 'DUMP_MY_ASSETS'
            , 'DUMP_MY_SMALL_LOGO'
        ], [
            $this->module->getName()
            , $this->getHeadline($this->module->getStudlyName())
            , $this->getSlug($this->module->getStudlyName())
            , $this->assetFolder()
            , $this->getLogoText($this->module->getStudlyName())
        ], $stub);
    }

    private function getSlug($str)
    {
        return Str::slug($str);
    }

    private function getLogoText($str)
    {
        return Str::upper(Str::substr($str, 0, 3));

    }

    private function getNamespace()
    {
        return str_replace("/", "\\", $this->folder);

    }

    private function getHeadLine($str)
    {
        $str = Str::replace("/", " ", $str);
        $str = Str::snake($str, "-");
        return Str::headline($str);
    }

    private function writeFile($path, $data)
    {
        if (file_exists($path) && !$this->isForce()) {
            $this->warn("File already exists [$path] !");
            if (!confirm("Do you want replace it?")) {
                return false;
            }
        }
        $this->ensureDirectoryExists($path);

        if (File::put($path, $data)) {
            $this->info("The file has been recorded to: $path");
        }
        return false;
    }

    private function ensureDirectoryExists($path)
    {
        $path = dirname($path);
        File::ensureDirectoryExists($path);
    }

    private function getStub($name)
    {
        $path = base_path("stubs/livewire-form-stubs/$name");
        if (!File::exists($path)) {
            $path = __DIR__ . "/../Stubs/$name";
        }
        if (!File::exists($path)) {
            $this->error("Invalid stub [$name] !");
            return false;
        }
        return file_get_contents($path);
    }

    private function generateVariables($fields)
    {
        $return = [];
        foreach ($fields as $k => $item) {
            $return[$k] = "$$k";
            if ($item["default"] !== null) {
                $return[$k] = "$$k = " . $item["default"];
            }
        }
        return $return;
    }

    private function generateRules($fields)
    {
        $return = [];
        foreach ($fields as $k => $item) {
            if ($item["rule"]) {
                $return[$k] = "'$k' => '" . $item["rule"] . "'";
            }
        }
        return $return;
    }

    private function generateShows($fields)
    {
        $return = [];
        foreach ($fields as $name => $field) {
            switch ($field["type_name"]) {
                case "json":
                case "array":
                    $return[$name] = '<tr>
            <th class="stt">' . $this->getHeadline($name) . ':</th>
            <td>{!! json_encode($data->' . $name . ',JSON_PRETTY_PRINT) !!}</td>
        </tr>';
                    break;
                default:
                    $return[$name] = '<tr>
            <th class="stt">' . $this->getHeadline($name) . ':</th>
            <td>{!! $data->' . $name . ' !!}</td>
        </tr>';
            }
        }

        return $return;

    }

    private function generateTableItems($fields)
    {
        $return = [];
        foreach($fields as $name =>$item){
            if($name !="id"){
                $return[$name] = '<x-lf.table.item :$fields name="' . $name . '">{{$item->' . $name . '}}</x-lf.table.item>';
            }
        }
        return $return;

    }

    private function generateTableLabels($fields)
    {
        $return = [];
        foreach($fields as $name =>$item){
            if($name != "id"){
                $return[$name] = '<x-lf.table.label :$fields name="' . $name . '">' . $this->getHeadline($name) . '</x-lf.table.label>';
            }
        }
        return $return;
    }

    private function generateForms($fields)
    {
        $return = [];
        foreach ($fields as $name => $item) {
            $id = $this->randomChars(3, $name);
            $type = $item["type_name"];
            switch ($type) {
                case "text":
                case "long-text":
                case "longtext":
                    $return[$name] = '<x-lf.form.textarea name="' . $name . '" label="' . $this->getHeadline($name) . '"  placeholder="' . $this->getHeadline($name) . ' ..." id="' . $id . '"/>';
                    break;
                case "boolean":
                    $return[$name] = '<x-lf.form.toggle name="' . $type . '"  label="' . $this->getHeadline($name) . '" :checked="$' . $name . ' == 1"  id="' . $id . '" />';
                    break;
                case "json":
                    $return[$name] = '<x-lf.form.json name="' . $type . '" label="' . $this->getHeadline($name) . '" :data="$' . $name . '" id="' . $id . '"/>';
                    break;
                default:
                    $return[$name] = '<x-lf.form.input type="' . $type . '" name="' . $name . '" class="md:w-1/2" label="' . $this->getHeadline($name) . '" placeholder="' . $this->getHeadline($name) . ' ..." id="' . $id . '" />';
                    break;
            }
        }

        return $return;
    }

    private function generateSetFields($fields)
    {
        $return = [];
        foreach ($fields as $k => $item) {
            $return[$k] = "\$this->$k = data_get(\$data,'$k');";
        }
        return $return;

    }

    private function generateSetData($fields)
    {
        $return = [];
        foreach ($fields as $k => $item) {
            $return[$k] = "'$k' => \$this->$k";
        }
        return $return;

    }

    private function generateArrFields($fields)
    {
        $return = [];
        foreach($fields as $name =>$item){
            if($name !="id"){
                $return[$name] = "'$name' => ['status' => ". $this->getBooleanText(!data_get($item,"hidden")).", 'label' => '" . $this->getHeadline($name) . "']";
            }
        }
        return $return;
    }
    private function getBooleanText($number)
    {
        return $number?'true':'false';
    }

    private function moduleView($path)
    {
        if (!$this->module) {
            $this->error("Invalid Module");
            return null;
        }
        $path = trim($path, "\\/ ");
        $path = "/$path";
        return $this->module->getExtraPath(config("modules.paths.generator.views.path", "resources/views") . $path);

    }

    private function livewireClass($file)
    {
        if (!$this->module) {
            $this->error("Invalid Module");
            return null;
        }
        $file = trim($file, "\/ ");
        $file = "$file";
        return $this->module->getExtraPath("app/Livewire/$this->folder/$file");
    }

    private function livewireView($file)
    {
        if (!$this->module) {
            $this->error("Invalid Module");
            return null;
        }
        $file = trim($file, "\/ ");
        $file = "$file";
        return $this->module->getExtraPath("resources/views/livewire/" . $this->viewFolder() . "/$file");
    }

    private function componentClass($file)
    {
        if (!$this->module) {
            $this->error("Invalid Module");
            return null;
        }
        $file = trim($file, "\/ ");
        $file = "/$file";
        return $this->module->getExtraPath(config("modules.paths.generator.component-class.path", "resources/views/components")) . $file;

    }

    private function componentView($file)
    {
        if (!$this->module) {
            $this->error("Invalid Module");
            return null;
        }
        $file = trim($file, "\/ ");
        $file = "/$file";
        return $this->module->getExtraPath(config("modules.paths.generator.component-view.path", "resources/views/components")) . $file;

    }

    private function generatePageName()
    {
        $return = [];
        foreach (explode("/", $this->folder) as $item) {
            $return[] = Str::headline($item);
        }
        return implode(" ", $return);
    }

    private function viewFolder()
    {
        $return = [];
        foreach (explode("/", $this->folder) as $item) {
            $return[] = Str::kebab($item);
        }
        return implode("/", $return);
    }

    private function getDotFolder()
    {
        $return = [];
        foreach (explode("/", $this->folder) as $item) {
            $return[] = Str::kebab($item);
        }
        return implode(".", $return);

    }

    private function assetFolder()
    {
        return config("modules.paths.generator.assets.path", "resources/assets");

    }
    private function getModulepath($path = "")
    {
        return $this->module->getPath() . "/$path";
    }
    private function assetsPath($path)
    {
        if (!$this->module) {
            $this->error("Invalid Module");
            return null;
        }
        $path = trim($path, "\\/ ");
        return $this->module->getExtraPath($this->assetFolder() . "/$path");

    }

    private function showNewLine($t = 0, $pre = "")
    {
        return Str::padRight("$pre\r\n", $t, "\t");
    }

    private function randomChars($number = 1, $pre = '')
    {
        if ($pre != "") {
            return $pre . "-" . substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", $number)), 0, $number);
        }
        return substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", $number)), 0, $number);
    }

    private function isForce()
    {
        if ($this->hasOption("force")) {
            return $this->option("force");
        }
        return false;
    }
}
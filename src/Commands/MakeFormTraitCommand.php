<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use function Laravel\Prompts\multiselect;

class MakeFormTraitCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-form-trait {name} {module} {--model=}';

    protected $description = 'Make FormTrait';

    public function handle()
    {
        $this->info("Make Form Trait");
        $this->initModule($this->argument("module"));
        $this->initPath($this->argument("name"));
        $this->initModel($this->path);
        $this->createFormTrait();
    }

    private function createFormTrait()
    {
        $pathSave = $this->getClassFile("FormTrait.php");
        $rules = $this->getRules();
        $listFields = $this->getListFields();
        $setFields = $this->getSetFields();
        $fieldData = $this->getFieldDatas();

        return $this->createFile($pathSave, 'FormTrait.php', [
            "DUMP_MY_FIELDS" => implode(", ", array_merge_recursive(['$record_id'],$listFields)),
            "DUMP_MY_RULES" => implode("," . $this->showNewLine(5), $rules),
            "DUMP_MY_SET_FIELDS" => implode($this->showNewLine(4), $setFields),
            "DUMP_MY_SET_DATA" => implode("," . $this->showNewLine(5), $fieldData),
        ]);
    }

    private function getFieldDatas()
    {
        $rt = [];
        foreach ($this->getFields() as $field => $item) {
            if (!$this->checkReservedField($field)) {
                $rt[$field] = "\"$field\" => \$this->$field";
            }
        }
        return $rt;
    }

    private function getSetFields()
    {
        $rt = [];
        foreach ($this->getFields() as $field => $item) {
            if (!$this->checkReservedField($field)) {
                $default = $this->getDefault($item);
                if ($default !== null) {
                    $rt[$field] = "\$this->$field = data_get(\$data, \"$field\", $default);";
                } else {
                    $rt[$field] = "\$this->$field = data_get(\$data, \"$field\");";
                }

            }
        }
        return $rt;
    }

    private function getListFields()
    {
        $rt = [];
        foreach ($this->getFields() as $field => $item) {
            if (!$this->checkReservedField($field)) {
                $default = $this->getDefault($item);
                if ($default) {
                    $rt[$field] = "$$field = $default";
                } else {
                    $rt[$field] = "$$field";
                }
            }
        }
        return $rt;
    }

    private function getRules()
    {
        $rt = [];
        foreach ($this->getFields() as $field => $item) {
            if (!$this->checkReservedField($field)) {
                $rt[$field] = "\"$field\" => \"$item->rule\"";
            }
        }
        return $rt;
    }

    private function getDefault($item)
    {
        $default = $item->default;
        switch ($item->type) {
            case "json":
                $default = '[]';
                break;
            case "boolean":
            case "integer":
            case "bigint":
            case "tinyint":
                $default = intval($default);
                break;
            default:
                if ($default) {
                    $default = "'$default'";
                }
                break;

        }
        return $default;
    }
}

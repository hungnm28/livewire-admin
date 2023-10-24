<?php

namespace Hungnm28\LivewireAdmin\Supports;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Nwidart\Modules\Facades\Module;

class MenuSupport
{
    public function getMenu($module = "")
    {
        $rt = [];

        $path = Module::getModulePath($module) . "Resources/views/components/menu/categories.blade.php";
        if (file_exists($path)) {
            $html = file_get_contents($path);
            $html = str_get_html($html);
            foreach ($html->find(".menu .item") as $item) {
                $row = [];
                foreach ($item->find(".link") as $link) {
                    if (preg_match('/\'([a-z.0-9]+)\'/', $link->href, $parent)) {
                        $routeName = $parent[1];
                        $row["route"] = $routeName;
                        $row["icon"] = '';
                        $row["label"] = '';
                        foreach ($link->find('.icon') as $icon) {
                            if (preg_match('/\'([a-z0-9-]+)\'/', $icon->text(), $icons)) {
                                $row["icon"] = $icons[1];
                            }
                        }
                        foreach ($link->find(".title") as $title) {
                            if ($title->text() != "") {
                                $row["label"] = $title->text();
                            }
                        }
                    }
                }
                if (!empty($row)) {
                    $row["children"] = [];
                    foreach ($item->find('.children . child') as $childNode) {
                        $child = [];
                        foreach ($childNode->find(".link") as $link) {
                            if (preg_match('/\'([a-z.0-9]+)\'/', $link->href, $parent)) {
                                $routeName = $parent[1];
                                $child["route"] = $routeName;
                                foreach ($link->find('.icon') as $icon) {
                                    if (preg_match('/\'([a-z.0-9]+)\'/', $icon->text(), $icons)) {
                                        $child["icon"] = $icons[1];
                                    }
                                }
                                foreach ($link->find(".title") as $title) {
                                    if ($title->text() != "") {
                                        $child["label"] = $title->text();
                                    }
                                }
                            }
                            if (!empty($child)) {
                                $row["children"][] = $child;
                            }
                        }
                    }
                    $rt[] = $row;
                }
            }
        }
        return $rt;
    }

    public function setMenu($data = [], $parent = -1, $sort = 0, $module = "admin")
    {
        $menus = self::getMenu($module);
        if (!isset($data["children"]) || !is_array($data["children"])) {
            $data["children"] = [];
        }
        // trường hợp Parent
        if ($parent == -1) {
            array_splice($menus, $sort, 0, [$data]);
        } else {
            if (isset($menus[$parent])) {
                $children = data_get($menus, $parent . ".children", []);
                array_splice($children, $sort, 0, [$data]);
                $menus[$parent]['children'] = $children;
            }
        }
        return $this->storeMenu($menus, $module);
    }

    public  function deleteMenu($key, $module)
    {
        $menus = self::getMenu($module);
        Arr::forget($menus, $key);
        return $this->storeMenu($menus, $module);
    }

    public  function storeMenu($data = [], $module = "")
    {
        $path = Module::getModulePath($module) . "Resources/views/components/menu/categories.blade.php";
        $itemStub = $this->getStub("Layouts/menu/menu-item.stub");
        $childStub = $this->getStub("Layouts/menu/menu-children.stub");
        $itemHTML ="";
        foreach($data as $row){

            $childHtml = "";
            if(!empty($orw["children"])){
                $childHtml = '<ul class="children">';
                foreach($row['children'] as $child){
                    $childHtml .= str_replace([
                        "DUMP_MY_ROUTE",
                        "DUMP_MY_ICON",
                        "DUMP_MY_LABEL"
                    ],[
                        $child['route'],
                        $child['icon'],
                        $child['label'],
                    ],$childStub);
                }//foreach($row['children'] as $child)
                $childHtml .= '</ul>';
            }
            $itemHTML .= str_replace([
                "DUMP_MY_ROUTE",
                "DUMP_MY_ICON",
                "DUMP_MY_LABEL",
                "DUMP_MY_CHILDREN"
            ],[
                $row['route'],
                $row['icon'],
                $row['label'],
                $childHtml

            ],$itemStub);

        }
        $stub = $this->getStub("Layouts/menu/categories.blade.php.stub",$module);
        $template = str_replace([
            "DUMP_MY_MENUS"
        ],[
            $itemHTML
        ],$stub);
        return file_put_contents($path,$template);
    }

    private function getStub($file)
    {
        $path = base_path('stubs/livewire-admin-stubs/' . $file);
        if (!File::exists($path)) {
            $path = __DIR__ . "/../Stubs/$file";
        }
        if (!File::exists($path)) {
            return false;
        }
        return file_get_contents($path);
    }
}

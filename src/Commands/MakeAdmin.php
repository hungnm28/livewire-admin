<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeAdmin extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-admin {module}';

    protected $description = 'Make Admin';


    public function handle()
    {
        $this->initModule($this->argument("module"));
        $pages = [
            "Settings" => [
                "classes" => ['Index.php'],
                "views" => ['index.blade.php']
            ]
            , "Icons" => [
                "classes" => ['Index.php', 'Create.php'],
                "views" => ['index.blade.php', 'create.blade.php']
            ]
            , "Menus" => [
                "classes" => ['Index.php', 'Create.php'],
                "views" => ['index.blade.php', 'create.blade.php', 'tree.blade.php']
            ]
            , "Permissions" => [
                "classes" => ['Index.php', 'FormTrait.php', 'Create.php', 'Edit.php', 'Show.php'],
                "views" => ['index.blade.php', 'create.blade.php', 'edit.blade.php', 'show.blade.php', 'tree.blade.php']
            ]
            , "Roles" => [
                "classes" => ['Index.php', 'FormTrait.php', 'Create.php', 'Edit.php', 'Show.php'],
                "views" => ['index.blade.php', 'create.blade.php', 'edit.blade.php', 'show.blade.php', 'permission-form.blade.php', 'show-permissions.blade.php']
            ]
            , "Admins" => [
                "classes" => ['Index.php', 'FormTrait.php', 'Create.php', 'Edit.php', 'Show.php'],
                "views" => ['index.blade.php', 'create.blade.php', 'edit.blade.php', 'show.blade.php']
            ]
            , "Users" => [
                "classes" => ['Index.php', 'FormTrait.php', 'Create.php', 'Edit.php', 'Show.php'],
                "views" => ['index.blade.php', 'create.blade.php', 'edit.blade.php', 'show.blade.php']
            ]
        ];
        $this->makePages($pages);
        $this->makeNavbar();
        $this->makeRoute();

    }

    private function makePages($pages)
    {

        foreach ($pages as $key => $item) {
            $this->makeView($key, $item['views']);
            $this->makeClass($key, $item['classes']);
        }
    }

    private function makeView($page, $data)
    {
        $page_slug = Str::slug($page);
        foreach ($data as $item) {
            $pathSave = $this->getModulepath("Resources/views/livewire/$page_slug/$item");
            $stub = "Admin/$page/$item.stub";
            $this->createFile(
                path: $pathSave
                , name: $item
                , stub: $stub
                , force: true
            );
        }
    }

    private function makeClass($page, $data)
    {
        foreach ($data as $item) {
            $pathSave = $this->getModulepath("Livewire/$page/$item");
            $stub = "Admin/$page/$item.stub";
            $this->createFile(
                path: $pathSave
                , name: $item
                , stub: $stub
                , force: true
            );
        }
    }

    private function makeNavbar()
    {
        $pathSave = $this->getModulepath("Resources/views/components/menu/categories.blade.php");
        $stub = "Admin/components/menu/categories.blade.php.stub";
        $this->createFile(
            path: $pathSave
            , name: 'categories.blade.php'
            , stub: $stub
            , force: true
        );
    }

    private function makeRoute(){
        $pathSave = $this->getModulepath("Routes/web.php");
        $stub = "Admin/Routes/web.php.stub";
        $this->createFile(
            path: $pathSave
            , name: 'categories.blade.php'
            , stub: $stub
            , force: true
        );
    }
}
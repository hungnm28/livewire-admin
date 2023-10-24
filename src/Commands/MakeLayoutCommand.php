<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use function Laravel\Prompts\select;

class MakeLayoutCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-layout {module}';
    private $themes = [
        'blue-chill' => '--c50: #f2f9f9;--c100: #ddeff0;--c200: #bfe0e2;--c300: #92cace;--c400: #5faab1;--c500: #438e96;--c600: #3b757f;--c700: #356169;--c800: #325158;--c900: #2d464c;--c950: #1a2c32;'
        , 'waikawa-gray' => '--c50: #f2f7fb;--c100: #e7f0f8;--c200: #d3e2f2;--c300: #b9cfe8;--c400: #9cb6dd;--c500: #839dd1;--c600: #6a7fc1;--c700: #6374ae;--c800: #4a5989;--c900: #414e6e;--c950: #262c40;'
        , 'wewak' => '--c50: #fdf3f4;--c100: #fbe8eb;--c200: #f6d5da;--c300: #ea9daa;--c400: #e58799;--c500: #d75c77;--c600: #c13d60;--c700: #a22e4f;--c800: #882947;--c900: #752642;--c950: #411020;'
        , 'purple-heart' => '--c50: #f3f1ff;--c100: #ebe5ff;--c200: #d9ceff;--c300: #bea6ff;--c400: #9f75ff;--c500: #843dff;--c600: #7916ff;--c700: #6b04fd;--c800: #5a03d5;--c900: #4b05ad;--c950: #2c0076;'
        , 'mantis' => '--c50: #f6faf3;--c100: #e9f5e3;--c200: #d3eac8;--c300: #afd89d;--c400: #82bd69;--c500: #61a146;--c600: #4c8435;--c700: #3d692c;--c800: #345427;--c900: #2b4522;--c950: #13250e;'
        , 'jade' => '--c50: #effef7;--c100: #dafeef;--c200: #b8fadd;--c300: #81f4c3;--c400: #43e5a0;--c500: #1acd81;--c600: #0fa968;--c700: #108554;--c800: #126945;--c900: #11563a;--c950: #03301f;'
        , 'vermilion' => '--c50: #fff7ec;--c100: #ffecd3;--c200: #ffd5a5;--c300: #ffb76d;--c400: #ff8d32;--c500: #ff6c0a;--c600: #f44f00;--c700: #cc3902;--c800: #a12d0b;--c900: #82280c;--c950: #461104;'
    ];
    private $themesSelect = [
        'blue-chill' => 'Blue Chill'
        , 'waikawa-gray' => 'Waikawa Gray'
        , 'wewak' => 'Wewak'
        , 'purple-heart' => 'Purple Heart'
        , 'mantis' => 'Mantis'
        , 'jade' => 'Jade'
        , 'vermilion' => 'Vermilion'
    ];

    protected $description = 'Make Layout';

    public function handle()
    {
        $this->info("Make Layout");
        $this->initModule($this->argument("module"));
        $this->createCategories();
        $this->createMenu();
        $this->createLayoutView();
        $this->createLayoutClass();
        $this->createIndexFile();
        $this->replaceProvider();
        $this->replaceRouteProvider();
        $this->replaceRoute();
        $this->createAssets();

    }



    private function replaceRoute()
    {
        $pathSave = $this->getModulepath("Routes/web.php");
        return $this->createFile(
            path: $pathSave,
            name: 'web.php',
            stub: 'Layouts/web.php.stub'
            , force: true
        );
    }

    private function replaceRouteProvider()
    {

        $pathSave = $this->getModulepath("Providers/RouteServiceProvider.php");
        return $this->createFile(
            path: $pathSave,
            name: 'RouteServiceProvider.php',
            stub: 'Providers/RouteServiceProvider.php.stub'
            , force: true
        );
    }

    private function replaceProvider()
    {

        $pathSave = $this->getModulepath("Providers/" . $this->getModuleName() . "ServiceProvider.php");
        return $this->createFile(
            path: $pathSave,
            name: 'ServiceProvider.php',
            stub: 'Providers/ModuleServiceProvider.php.stub'
            , force: true
        );
    }

    private function createIndexFile()
    {
        $pathSave = $this->getModulepath("Resources/views/index.blade.php");
        return $this->createFile(
            path: $pathSave,
            name: 'LayoutMaster.php',
            stub: 'Layouts/index.blade.php.stub'
            , force: true
        );
    }

    private function createLayoutClass()
    {
        $pathSave = $this->getModulepath("Views/Components/LayoutMaster.php");
        return $this->createFile(
            path: $pathSave,
            name: 'LayoutMaster.php',
            stub: 'Layouts/LayoutMaster.php.stub'
            , force: true
        );
    }

    private function createLayoutView()
    {
        $pathSave = $this->getModulepath("Resources/views/layouts/master.blade.php");
        $themesSelect = [];
        foreach ($this->themes as $k => $val) {
            $themesSelect[$k] = Str::headline($k);
        }
        ksort($themesSelect);
        $kT = select(
            label: 'What is theme color?',
            options: $themesSelect,
            default: 'blue-chill',
            hint: 'Select theme color.'
        );
        $theme = data_get($this->themes, $kT, $this->themes['blue-chill']);
        return $this->createFile(
            path: $pathSave,
            name: 'master.blade.php',
            data: [
                'DUMP_MY_CSS_ROOT' => str_replace(';', ';' . $this->showNewLine(4), $theme)
            ],
            stub: 'Layouts/master.blade.php.stub'
            , force: true
        );
    }

    private function createCategories()
    {
        $pathSave = $this->getModulepath("Resources/views/components/menu/categories.blade.php");
        $this->writeFile($pathSave, '<ul class="menu"></ul>', true);
    }

    private function createMenu()
    {
        $pathSave = $this->getModulepath("Resources/views/components/menu/index.blade.php");
        return $this->createFile(
            path: $pathSave,
            name: 'index.blade.php',
            stub: 'Layouts/menu/index.blade.php.stub'
            , force: true
        );

    }

    private function createAssets()
    {
        $pathSave = $this->getModulepath("Resources/assets/js/app.js");
        $this->createFile(
            path: $pathSave,
            name: 'app.js',
            stub: 'Layouts/assets/js/app.js.stub'
            , force: true
        );

        $pathSave = $this->getModulepath("Resources/assets/sass/app.scss");
        $this->createFile(
            path: $pathSave,
            name: 'app.scss',
            stub: 'Layouts/assets/sass/app.scss.stub'
            , force: true
        );

        $pathSave = $this->getModulepath("Resources/assets/sass/_layout.scss");
        $this->createFile(
            path: $pathSave,
            name: '_layout.scss',
            stub: 'Layouts/assets/sass/_layout.scss.stub'
            , force: true
        );
        $this->alert("Add this text to vite.config.js: ");
        $this->info('Modules/' . $this->getModuleName() . '/Resources/assets/sass/app.scss');
        $this->info('Modules/' . $this->getModuleName() . '/Resources/assets/js/app.js');
    }

}

<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\WithCommandTrait;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleSetup extends Command
{
    use WithCommandTrait;

    protected $signature = 'la:module-setup {name} {--force}';

    protected $description = 'Setup module';

    public function handle()
    {
        $this->info("Setup");
        $moduleName = $this->argument("name");
        $this->initModule($moduleName);

        $this->configNavbar();
        $this->configPermission();
        $this->initModulePermission();
        $this->updateProvider();
        $this->initModuleJson();

        return Command::SUCCESS;
    }

    private function configNavbar()
    {
        $moduleName = $this->getModuleName();
        (new Filesystem)->copy(__DIR__ . '/../../publishes/Config/menu.php', module_path($moduleName, 'Config/menu.php'));
    }

    private function configPermission()
    {
        $moduleName = $this->getModuleName();
        (new Filesystem)->copy(__DIR__ . '/../../publishes/Config/permission.php', module_path($moduleName, 'Config/permission.php'));
    }

    private function initModulePermission()
    {
        $name = $this->getModuleName();
        $stub = $this->getStub("Providers/PermissionServiceProvider.php.stub");
        $template = $this->replaceModuleStub($stub);
        $this->writeFile(module_path($name, "Providers/PermissionServiceProvider.php"), $template);
    }


    private function updateProvider()
    {
        $moduleName = $this->module->getName();
        $pathFile = module_path($moduleName, "Providers/" . $moduleName . "ServiceProvider.php");
        if (Str::contains(file_get_contents($pathFile), "Blade::componentNamespace")) {
            $confirm = $this->confirm("ServicePorivider already updated before, do you want replace it?", false);
            if (!$confirm) {
                return false;
            }
        }
        $stub = $this->getStub("Providers/ModuleServiceProvider.php.stub");
        $stub = $this->replaceModuleStub($stub);

        $this->writeFile($pathFile, $stub);
        return true;
    }

    private function initModuleJson()
    {
        $name = $this->getModuleName();
        $stub = $this->getStub("module.json.stub");
        $template = $this->replaceModuleStub($stub);
        $this->writeFile(module_path($name, "module.json"), $template);
    }
}

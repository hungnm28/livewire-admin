<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeRouteCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-route {name} {module} {--model=}';

    protected $description = 'Make Route';

    public function handle()
    {
        $this->info("Make Route");
        $this->initModule($this->argument("module"));
        $this->initPath($this->argument("name"));
        $this->installRoute();

    }

    protected function installRoute()
    {
        $pathSave = $this->getModulepath('Routes/web.php');
        if(!file_exists($pathSave)){
            $this->error("Routes not exits");
            return false;
        }
        $routes = file_get_contents($pathSave);
        $route = $this->getStub("route.php.stub");
        $route = $this->replaceStub($route);
        if (!Str::contains($routes, '->name(".' . $this->getPageLowerName() . '")')) {
           file_put_contents($pathSave,$route,FILE_APPEND);
        }
        return true;
    }
}

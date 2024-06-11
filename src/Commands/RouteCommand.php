<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use function Laravel\Prompts\select;

class RouteCommand extends Command
{
    protected $signature = 'la:route {folder} {module} {--model=}';
    protected $description = 'Generate Auth';

    use CommandTrait;

    public function handle()
    {
        $this->init();
        return $this->installRoute();
    }

    protected function installRoute()
    {
        $pathSave = $this->getModulepath('Routes/web.php');
        if(!file_exists($pathSave)){
            $this->error("Routes not exits");
            return false;
        }
        $routes = file_get_contents($pathSave);
        if(Str::contains($routes, '->name(".' . $this->getDotFolder() . '")')){
            $this->warn("This route already exits!");
            return false;
        }

        $route = $this->getTemplate('route.php.stub');

        file_put_contents($pathSave,$route,FILE_APPEND);

        $this->info('The router has been created!');
        return true;
    }
}
<?php

namespace Hungnm28\LivewireAdmin\Commands;
use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AuthCommand extends Command
{
    protected $signature = 'la:auth {--force}';
    protected $description = 'Generate Auth';

    use CommandTrait;

    public function handle()
    {
        $this->alert("Generate Auth");
        $this->init();
        $this->createPermission();
        $this->createMiddleware();
    }

    private function createPermission()
    {
        $stub = $this->getStub("Providers/PermissionsServiceProvider.php.stub");
        if(!$stub){
            $this->error("Invalid PermissionServiceProvider Stub");
            return false;
        }
        $pathSave = app_path("Providers/PermissionsServiceProvider.php");
        $this->writeFile($pathSave,$stub);
        $this->installServiceProviderAfter('JetstreamServiceProvider', 'PermissionsServiceProvider');
        return true;
    }

    private function installServiceProviderAfter($after, $name)
    {
        $pathConfig = base_path("bootstrap/providers.php");
        if (!Str::contains($appConfig = file_get_contents($pathConfig), 'App\\Providers\\' . $name . '::class')) {
            file_put_contents($pathConfig, str_replace(
                'App\\Providers\\' . $after . '::class,',
                'App\\Providers\\' . $after . '::class,' . PHP_EOL . '    App\\Providers\\' . $name . '::class,',
                $appConfig
            ));
        }
    }

    private function createMiddleware()
    {
        $this->info("Create AdminMiddleware");
        $pathSave = app_path("Http/Middleware/AdminMiddleware.php");
        $stub = $this->getStub("Middlewares/AdminMiddleware.php.stub");
        if(!$stub){
            $this->error("Invalid AdminMiddleware stub");
            return false;
        }
        $this->writeFile($pathSave,$stub);
        $this->installMiddlewareAfter('withMiddleware(function (Middleware $middleware) {', '$middleware->group("admin",[\App\Http\Middleware\AdminMiddleware::class]);');
        return true;
    }

    private function installMiddlewareAfter($after, $name)
    {
        $path = base_path("bootstrap/app.php");
        if (!Str::contains($appConfig = file_get_contents($path), $name)) {
            file_put_contents($path, str_replace(
                $after,
                $after . PHP_EOL . "\t\t" . $name,
                $appConfig
            ));
        }
    }
}
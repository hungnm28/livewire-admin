<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use function Laravel\Prompts\confirm;

class MakeAuthCommand extends Command
{
    protected $signature = 'la:make-auth';

    protected $description = 'Make AdminMiddleware, PermissionProvider';

    public function handle()
    {
        $this->createMiddleware();
        $this->createProvider();
    }

    private function createProvider(){
        $this->info("Make: PermissionsServiceProvider");
        $pathSave = app_path("Providers/PermissionsServiceProvider.php");
        if (file_exists($pathSave)) {
            $confirm = confirm("PermissionsServiceProvider file already exists, would you like replace it?", false);
            if (!$confirm) {
                return false;
            }
        }

        (new Filesystem())->copy(__DIR__ ."/../Stubs/Layouts/PermissionsServiceProvider.php.stub",$pathSave);
        // register provider
        $this->installServiceProviderAfter('JetstreamServiceProvider', 'PermissionsServiceProvider');
    }

    private function installServiceProviderAfter($after, $name)
    {
        if (!Str::contains($appConfig = file_get_contents(config_path('app.php')), 'App\\Providers\\' . $name . '::class')) {
            file_put_contents(config_path('app.php'), str_replace(
                'App\\Providers\\' . $after . '::class,',
                'App\\Providers\\' . $after . '::class,' . PHP_EOL . '        App\\Providers\\' . $name . '::class,',
                $appConfig
            ));
        }
    }

    private function createMiddleware()
    {
        $this->info("Create AdminMiddleware");
        $pathSave = app_path("Http/Middleware/AdminMiddleware.php");
        if (file_exists($pathSave)) {
            $confirm = confirm("AdminMiddleware file already exists, would you like replace it?", false);
            if (!$confirm) {
                return false;
            }
        }
        (new Filesystem())->copy(__DIR__ . "/../Stubs/Layouts/AdminMiddleware.php.stub", $pathSave);
        $this->installMiddlewareAfter("'auth' => \App\Http\Middleware\Authenticate::class,", "'admin' => \App\Http\Middleware\AdminMiddleware::class,");
        return true;
    }

    private function installMiddlewareAfter($after, $name)
    {
        $path = app_path("Http/Kernel.php");
        if (!Str::contains($appConfig = file_get_contents($path), $name)) {
            file_put_contents($path, str_replace(
                $after,
                $after . PHP_EOL . "\t\t" . $name,
                $appConfig
            ));
        }
    }

}

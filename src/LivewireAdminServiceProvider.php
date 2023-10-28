<?php

namespace Hungnm28\LivewireAdmin;

use Hungnm28\LivewireAdmin\Supports\MenuSupport;
use Hungnm28\LivewireAdmin\Supports\RouteSupport;
use Illuminate\Support\ServiceProvider;

class LivewireAdminServiceProvider extends ServiceProvider
{
    protected $commands = [
        Commands\ModelCommand::class,
        Commands\MakePageCommand::class,
        Commands\MakeFormTraitCommand::class,
        Commands\MakeCreateCommand::class,
        Commands\MakeEditCommand::class,
        Commands\MakeShowCommand::class,
        Commands\MakeIndexCommand::class,
        Commands\MakeRouteCommand::class,
        Commands\MakeLayoutCommand::class,
        Commands\MakeAuthCommand::class,
    ];

    public function register()
    {
        parent::register();
        $this->app->bind('route-support', function ($app) {
            return new RouteSupport();
        });
        $this->app->bind('menu-support', function ($app) {
            return new MenuSupport();
        });
    }

    public function boot()
    {
        $this->configureCommands();
        $this->registerPublishing();
    }

    protected function configureCommands()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->commands($this->commands);
    }

    protected function registerPublishing()
    {
        $this->publishes([
            __DIR__ . '/../publishes/Casts' => app_path("Casts"),
            __DIR__ . '/../publishes/database' => database_path("migrations"),
            __DIR__ . '/../publishes/Traits' => app_path("Traits"),
            __DIR__ . '/../publishes/Models' => app_path("Models"),
            __DIR__ . '/../publishes/lf' => base_path("resources/views/components/lf"),
            __DIR__ . '/../publishes/assets' => base_path("resources/assets"),
            __DIR__ . '/../publishes/icons/icons.svg' => public_path("assets/images/icons.svg")
        ], 'livewire-admin');
    }

}

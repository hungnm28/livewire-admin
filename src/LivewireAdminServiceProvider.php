<?php

namespace Hungnm28\LivewireAdmin;

use Illuminate\Support\ServiceProvider;

class LivewireAdminServiceProvider extends ServiceProvider
{

    protected $commands = [
        Commands\UserCommand::class,
        Commands\AuthCommand::class,
        Commands\ModelCommand::class,
        Commands\LayoutCommand::class,
        Commands\PageCommand::class,
        Commands\RouteCommand::class,
    ];

    public function register()
    {
        parent::register();

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
            __DIR__ . '/../publishes/Traits' => app_path("Traits"),
            __DIR__ . '/../publishes/lf' => base_path("resources/views/components/lf"),
            __DIR__ . '/../publishes/assets' => base_path("resources/assets"),
            __DIR__ . '/../publishes/Models' => app_path("Models"),
            __DIR__ . '/../publishes/database' => database_path(),
        ], 'livewire-admin');
        $this->publishes([
            __DIR__ . '/../publishes/vite.config.js' => base_path('vite.config.js'),
        ], 'livewire-admin-vite');
    }
}
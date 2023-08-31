<?php

namespace Hungnm28\LivewireAdmin;

use Illuminate\Support\ServiceProvider;

class LivewireAdminServiceProvider extends ServiceProvider
{
    protected $commands = [
            Commands\MakeModule::class
            ,Commands\ModuleLayout::class
            ,Commands\ModuleSetup::class
            ,Commands\ModelCommand::class
            ,Commands\MakeFormTrait::class
            ,Commands\MakeCreate::class
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
        ], 'livewire-admin');
    }

}

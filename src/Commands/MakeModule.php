<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\WithCommandTrait;
use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;


class MakeModule extends Command
{
    use WithCommandTrait;

    protected $signature = 'la:make-module {name} {type=admin} {--force}';

    protected $description = 'Create new module';

    public function handle()
    {
        $moduleName = $this->argument("name");
        $check = Module::has($moduleName);
        if (!$check) {
            $this->info("Make new Module: $moduleName");
            $this->call("module:make", ["name" => [$moduleName]]);
        } else {
            $this->error("Module exits: $moduleName");
        }
        return Command::SUCCESS;
    }
}

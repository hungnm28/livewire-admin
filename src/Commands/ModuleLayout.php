<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\text;

class ModuleLayout extends Command
{

    protected $signature = 'la:module-layout {name} {--force}';

    protected $description = 'Generate layout';

    public function handle()
    {
        $this->info("Generate layout");
        $moduleName = $this->argument("name");
        $this->initModule($moduleName);

        return Command::SUCCESS;
    }

    private function generateLayout(){

    }

    private function generateHeader(){

    }

    private function generateMenu(){

    }
}

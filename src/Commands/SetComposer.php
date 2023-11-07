<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Illuminate\Console\Command;
use function Laravel\Prompts\confirm;

class SetComposer extends Command
{
    protected $signature = 'la:set-composer';

    protected $description = 'Update composer.json for laravel module';

    public function handle()
    {
        $comfirm = confirm("Do you want update composer.json for laravel-module?",true);
        if ($comfirm) {
            $currentComposer = file_get_contents(base_path() . "/composer.json");
            $arrayComposer = json_decode(trim($currentComposer),true);
            if(!isset($arrayComposer["autoload"]["psr-4"]['Modules\\'])){
                $arrayComposer["autoload"]["psr-4"]['Modules\\'] = 'Modules/';
            }
            file_put_contents(base_path() . "/composer.json",json_encode($arrayComposer,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
        return Command::SUCCESS;

    }

}
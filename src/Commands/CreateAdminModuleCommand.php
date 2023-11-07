<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\text;

class CreateAdminModuleCommand extends Command
{
    protected $signature = 'la:create-admin-module';

    protected $description = 'Create Admin Module';

    public function handle()
    {
        $moduleName = text("Name of Admin module", 'Admin module name...', 'Admin', true);
        $check = Module::has($moduleName);
        if (!$check) {
            $this->call("module:make", ["name" => [$moduleName]]);
        }
        $this->call('la:make-auth');
        $this->call('la:make-layout', ['module' => $moduleName]);
      //  $this->call('la:make-admin', ['module' => $moduleName]);
        $confirm = confirm("Would you like create new admin user?");
        if($confirm){
            $this->call('la:create-user');
        }
        $confirm = confirm("Would you like make base admin pages?");
        if($confirm){
            $this->call('la:make-admin',['module' => $moduleName]);
        }

    }

}
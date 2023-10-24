<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use function Laravel\Prompts\multiselect;

class MakePageCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:make-page {name} {module} {--model=}';

    protected $description = 'Make page';

    public function handle()
    {
        $pages = multiselect(label: 'What do you want to make?',
            options: [
                'la:make-index' => 'Index Page',
                'la:make-form-trait' => 'Create Form Trait',
                'la:make-create' => 'Create Page',
                'la:make-edit' => 'Edit Page',
                'la:make-show' => 'Show Page',
                'la:make-route' => 'Add Route',
            ],
            default: ['la:make-index', 'la:make-form-trait', 'la:make-create', 'la:make-edit', 'la:make-show', 'la:make-route'],
        );

        foreach($pages as  $com){
            $this->call($com,[
                "name"=>$this->argument("name"),
                "module"=>$this->argument("module"),
                "--model"=>$this->option("model"),
            ]);
        }
    }

}

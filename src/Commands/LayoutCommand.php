<?php

namespace Hungnm28\LivewireAdmin\Commands;

use Hungnm28\LivewireAdmin\Supports\ModelSupport;
use Hungnm28\LivewireAdmin\Traits\CommandTrait;
use Illuminate\Console\Command;
use Nwidart\Modules\Facades\Module;
use function Laravel\Prompts\select;

class LayoutCommand extends Command
{
    use CommandTrait;

    protected $signature = 'la:layout {module} {--force}';
    protected $description = 'Generate Layout';
    protected $themes = [
        'rose' => '--c50: #fff1f2; --c100: #ffe4e6; --c200: #fecdd3; --c300: #fda4af; --c400: #fb7185; --c500: #f43f5e; --c600: #e11d48; --c700: #be123c; --c800: #9f1239; --c900: #881337; --c950: #4c0519;',
        'pink' => '--c50: #fdf2f8; --c100: #fce7f3; --c200: #fbcfe8; --c300: #f9a8d4; --c400: #f472b6; --c500: #ec4899; --c600: #db2777; --c700: #be185d; --c800: #9d174d; --c900: #831843; --c950: #500724;',
        'fuchsia' => '--c50: #fdf4ff; --c100: #fae8ff; --c200: #f5d0fe; --c300: #f0abfc; --c400: #e879f9; --c500: #d946ef; --c600: #c026d3; --c700: #a21caf; --c800: #86198f; --c900: #701a75; --c950: #4a044e;',
        'purple' => '--c50: #faf5ff; --c100: #f3e8ff; --c200: #e9d5ff; --c300: #d8b4fe; --c400: #c084fc; --c500: #a855f7; --c600: #9333ea; --c700: #7e22ce; --c800: #6b21a8; --c900: #581c87; --c950: #3b0764;',
        'violet' => '--c50: #f5f3ff; --c100: #ede9fe; --c200: #ddd6fe; --c300: #c4b5fd; --c400: #a78bfa; --c500: #8b5cf6; --c600: #7c3aed; --c700: #6d28d9; --c800: #5b21b6; --c900: #4c1d95; --c950: #2e1065;',
        'indigo' => '--c50: #eef2ff; --c100: #e0e7ff; --c200: #c7d2fe; --c300: #a5b4fc; --c400: #818cf8; --c500: #6366f1; --c600: #4f46e5; --c700: #4338ca; --c800: #3730a3; --c900: #312e81; --c950: #1e1b4b;',
        'blue' => '--c50: #eff6ff; --c100: #dbeafe; --c200: #bfdbfe; --c300: #93c5fd; --c400: #60a5fa; --c500: #3b82f6; --c600: #2563eb; --c700: #1d4ed8; --c800: #1e40af; --c900: #1e3a8a; --c950: #172554;',
        'sky' => '--c50: #f0f9ff; --c100: #e0f2fe; --c200: #bae6fd; --c300: #7dd3fc; --c400: #38bdf8; --c500: #0ea5e9; --c600: #0284c7; --c700: #0369a1; --c800: #075985; --c900: #0c4a6e; --c950: #082f49;',
        'cyan' => '--c50: #ecfeff; --c100: #cffafe; --c200: #a5f3fc; --c300: #67e8f9; --c400: #22d3ee; --c500: #06b6d4; --c600: #0891b2; --c700: #0e7490; --c800: #155e75; --c900: #164e63; --c950: #083344;',
        'teal' => '--c50: #f0fdfa; --c100: #ccfbf1; --c200: #99f6e4; --c300: #5eead4; --c400: #2dd4bf; --c500: #14b8a6; --c600: #0d9488; --c700: #0f766e; --c800: #115e59; --c900: #134e4a; --c950: #042f2e;',
        'emerald' => '--c50: #ecfdf5; --c100: #d1fae5; --c200: #a7f3d0; --c300: #6ee7b7; --c400: #34d399; --c500: #10b981; --c600: #059669; --c700: #047857; --c800: #065f46; --c900: #064e3b; --c950: #022c22;',
        'green' => '--c50: #f0fdf4; --c100: #dcfce7; --c200: #bbf7d0; --c300: #86efac; --c400: #4ade80; --c500: #22c55e; --c600: #16a34a; --c700: #15803d; --c800: #166534; --c900: #14532d; --c950: #052e16;',
        'lime' => '--c50: #f7fee7; --c100: #ecfccb; --c200: #d9f99d; --c300: #bef264; --c400: #a3e635; --c500: #84cc16; --c600: #65a30d; --c700: #4d7c0f; --c800: #3f6212; --c900: #365314; --c950: #1a2e05;',
        'yellow' => '--c50: #fefce8; --c100: #fef9c3; --c200: #fef08a; --c300: #fde047; --c400: #facc15; --c500: #eab308; --c600: #ca8a04; --c700: #a16207; --c800: #854d0e; --c900: #713f12; --c950: #422006;',
        'amber' => '--c50: #fffbeb; --c100: #fef3c7; --c200: #fde68a; --c300: #fcd34d; --c400: #fbbf24; --c500: #f59e0b; --c600: #d97706; --c700: #b45309; --c800: #92400e; --c900: #78350f; --c950: #451a03;',
        'orange' => '--c50: #fff7ed; --c100: #ffedd5; --c200: #fed7aa; --c300: #fdba74; --c400: #fb923c; --c500: #f97316; --c600: #ea580c; --c700: #c2410c; --c800: #9a3412; --c900: #7c2d12; --c950: #431407;',
        'red' => '--c50: #fef2f2; --c100: #fee2e2; --c200: #fecaca; --c300: #fca5a5; --c400: #f87171; --c500: #ef4444; --c600: #dc2626; --c700: #b91c1c; --c800: #991b1b; --c900: #7f1d1d; --c950: #450a0a;',
        'stone' => '--c50: #fafaf9; --c100: #f5f5f4; --c200: #e7e5e4; --c300: #d6d3d1; --c400: #a8a29e; --c500: #78716c; --c600: #57534e; --c700: #44403c; --c800: #292524; --c900: #1c1917; --c950: #0c0a09;',
        'neutral' => '--c50: #fafafa; --c100: #f5f5f5; --c200: #e5e5e5; --c300: #d4d4d4; --c400: #a3a3a3; --c500: #737373; --c600: #525252; --c700: #404040; --c800: #262626; --c900: #171717; --c950: #0a0a0a;',
        'gray' => '--c50: #f9fafb; --c100: #f3f4f6; --c200: #e5e7eb; --c300: #d1d5db; --c400: #9ca3af; --c500: #6b7280; --c600: #4b5563; --c700: #374151; --c800: #1f2937; --c900: #111827; --c950: #030712;',
        'slate' => '--c50: #f8fafc; --c100: #f1f5f9; --c200: #e2e8f0; --c300: #cbd5e1; --c400: #94a3b8; --c500: #64748b; --c600: #475569; --c700: #334155; --c800: #1e293b; --c900: #0f172a; --c950: #020617;'
    ];

    public function handle()
    {
        $this->alert("Generate Layout: " . $this->argument("module"));
        $this->init();
        $this->createRoute();
        $this->createCategories();
        $this->createMenu();
        $this->createAssets();
        $this->createIndex();
        $this->createLayout();
    }

    private function createRoute()
    {
        if(!$this->module){
            $this->error("Invalid Module [".$this->argument('module')."]!");
        }
        $stub = $this->getTemplate("Providers/RouteServiceProvider.php.stub");
        $pathSave = $this->module->getExtraPath("app/Providers/RouteServiceProvider.php");
        $this->writeFile($pathSave,$stub);

        $stub = $this->getTemplate("Providers/web.php.stub");
        $pathSave = $this->module->getExtraPath("routes/web.php");
        if($stub){
            $this->writeFile($pathSave,$stub);
        }
    }

    private function createMenu()
    {
        $pathSave = $this->componentView("/menu/index.blade.php");
        $stub = $this->getTemplate("Layouts/menu/index.blade.php.stub");
        if ($pathSave && $stub) {
            return $this->writeFile($pathSave, $stub);
        }
        return false;
    }
    private function createCategories()
    {
        $pathSave = $this->componentView("/menu/categories.blade.php");
        if ($pathSave) {
            return $this->writeFile($pathSave, '<ul class="menu"></ul>');
        }
        return false;
    }

    private function createAssets()
    {
        $this->line("Create Assets");
        $path = $this->assetsPath("js/app.js");
        $stub = $this->getStub("Layouts/assets/js/app.js.stub");
        if ($stub) {
            $this->writeFile($path, $stub);
        }
        $path = $this->assetsPath("sass/_layout.scss");
        $stub = $this->getStub("Layouts/assets/sass/_layout.scss.stub");
        if ($stub) {
            $this->writeFile($path, $stub);
        }
        $pathApp = $this->assetsPath("sass/app.scss");
        $stubApp = $this->getStub("Layouts/assets/sass/app.scss.stub");
        if ($stubApp) {
            $this->writeFile($pathApp, $stubApp);
        }

    }

    private function createIndex()
    {
        $stubView = $this->getTemplate("Layouts/index.blade.php.stub");
        $pathView = $this->moduleView("index.blade.php");
        if ($stubView && $pathView) {
            $this->writeFile($pathView, $stubView);
        }
    }

    private function createLayout()
    {
        $viewStub = $this->getTemplate("Layouts/master.blade.php.stub");
        $viewPath = $this->moduleView("layouts/master.blade.php");
        if (!$viewPath || !$viewStub) {
            return false;
        }
        $themesSelect = [];
        foreach ($this->themes as $k => $val) {
            $themesSelect[$k] = $this->getHeadLine($k);
        }
        ksort($themesSelect);
        $kT = select(
            label: 'What is theme color?',
            options: $themesSelect,
            default: 'green',
            hint: 'Select theme color.'
        );
        $theme = data_get($this->themes, $kT, $this->themes['green']);
        $viewStub = str_replace("DUMP_MY_CSS_ROOT", $theme, $viewStub);
        $this->writeFile($viewPath, $viewStub);

        $classStub = $this->getTemplate("Layouts/LayoutMaster.php.stub");
        $pathClass = $this->componentClass("LayoutMaster.php");
        $this->writeFile($pathClass, $classStub);

    }
}
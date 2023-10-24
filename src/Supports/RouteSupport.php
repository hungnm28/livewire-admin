<?php

namespace Hungnm28\LivewireAdmin\Supports;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteSupport
{

    public function getNames($module=""){
        $data = [];
        foreach (Route::getRoutes() as $route) {
            if($module !=""){
                if (Str::is( "$module.*", $route->getName())) {
                    $data[] = $route->getName();
                }
            }else{
                $data[] = $route->getName();
            }

        }
        return $data;
    }
}

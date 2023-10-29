<?php

namespace Hungnm28\LivewireAdmin\Supports;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class RouteSupport
{

    public function getNames($module=""){
        $data = [];
        foreach (Route::getRoutes() as $route) {
            $routeName = "";
            if($module !=""){
                if (Str::is( "$module.*", $route->getName())) {
                    $routeName= $route->getName();
                }
            }else{
                $routeName = $route->getName();
            }
            if($routeName !=""){
                $data[] = $routeName;
            }

        }
        return $data;
    }
}

<?php

namespace Hungnm28\LivewireAdmin\Supports;

use Illuminate\Support\Facades\Storage;

class MenuSupport
{
    public static function getMenu($module=""){
        $rt = [];
        $data = Storage::get("menus/$module.cfn");
        if($data){
            $rt = json_decode($data,1);
        }
        return $rt;
    }

    public static function storeMenu($data=[],$module=""){
        return Storage::put("menus/$module.cfn",json_encode($data));
    }
}

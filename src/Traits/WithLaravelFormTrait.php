<?php

namespace Hungnm28\LivewireAdmin\Traits;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;

trait WithLaravelFormTrait
{
    use AuthorizesRequests;
    public $record_id;
    public $done = 0;

    public function addJson($name, $key,$value)
    {
        $key = trim($key);
        $value = trim($value);
        if ($value == ''||$key =='' ) return false;
        if (isset($this->$name)) {
            $this->$name[$key] = $value;
        }
    }

    public function addItem($name, $param)
    {
        $param = trim($param);
        if ($param == '') return false;
        if (isset($this->$name)) {
            $data = $this->$name;
            Arr::add($data, $name, $param);
            $data[] = $param;
            $this->$name = array_keys(array_flip($data));
        } else {
            if (Str::contains($name, ".")) {
                $this->addDotItem($name, $param);
            }
        }
    }

    private function addDotItem($name, $param)
    {
        $arrDots = explode(".", $name);
        $val = $arrDots[0];
        $key = Str::after($name, $val . ".");
        if (isset($this->$val)) {
            $data = data_get($this->$val, $key);
            $data[] = $param;
            data_set($this->$val, $key, array_keys(array_flip($data)));
        }
    }
    public function removeItem($val, $k)
    {
        if (isset($this->$val) && isset($this->$val[$k])) {
            unset($this->$val[$k]);
            $this->$val = array_values($this->$val);
        } else {

            $this->removeDotItem($val, $k);
        }

    }
    private function removeDotItem($name, $k)
    {
        $arrDots = explode(".", $name);
        $val = $arrDots[0];
        $key = Str::after($name, $val . ".");
        if (isset($this->$val)) {
            $data = data_get($this->$val, $key);
            if (isset($data[$k])) {
                unset($data[$k]);
                $data = array_keys(array_flip($data));
                data_set($this->$val, $key, $data);
            }
        }
    }

    public function redirectForm($route, $id=0, $params = [])
    {
        switch ($this->done) {
            case 0:
                $this->redirect(route("$route.listing", $params));
                break;
            case 1:
                $this->redirect(route("$route.create", $params));
                break;
            case 2:
                if($id>0){
                    $params["record_id"] = $id;
                    $this->redirect(route("$route.show", $params));
                    break;
                }

            case 3:
                if($id >0){
                    $params["record_id"] = $id;
                    $this->redirect(route("$route.edit", $params));
                    break;
                }

            default:
                $this->redirect(route($route));
        }
    }
    private function savePermission($module,$data){
        $this->onlyLocalhost();
        $module = Module::findOrFail($module);
        $str = "<?php \nreturn [\n\t'permissions' => [ \n";
        foreach($data as $key =>$label){
            $str .= "\t\t\t'$key' => '$label',\n";
        }
        $str.="\t\t]\n];";
        $configPath = $module->getPath() . "/Config/permission.php";
        file_put_contents($configPath,$str);
    }

    private function saveNavbar($module,$data){
        $module = Module::findOrFail($module);

        $navString = "<?php \n return [\n\t'menu' => [ \n ";
        foreach($data as $nav){
            $navString .= "\t\t[ \n";
            $navString .= "\t\t\t'label' => '". $nav['label'] . "',\n";
            $navString .= "\t\t\t'icon' => '". $nav['icon'] . "',\n";
            $navString .= "\t\t\t'route' => '". $nav['route'] . "',\n";
            $navString .= "\t\t\t'permission' => '". $nav['permission'] . "',\n";
            $childrenText = "[\n";
            $children = data_get($nav,"children",[]);
            if(!empty($children)){
                foreach($children as $child){
                    $childrenText.= "\t\t\t\t[ \n";
                    $childrenText .= "\t\t\t\t\t'label' => '". $child['label'] . "',\n";
                    $childrenText .= "\t\t\t\t\t'icon' => '". $child['icon'] . "',\n";
                    $childrenText .= "\t\t\t\t\t'route' => '". $child['route'] . "',\n";
                    $childrenText .= "\t\t\t\t\t'permission' => '". $child['permission'] . "',\n";
                    $childrenText.="\t\t\t\t],\n";

                }
            }
            $childrenText .= "\t\t\t],\n";
            $navString .= "\t\t\t'children' => " . $childrenText;
            $navString.="\t\t],\n";
        }
        $navString .="\n\t]\n];";
        $configPath = $module->getPath() . "/Config/menu.php";
        file_put_contents($configPath,$navString);

    }


    public function postSort($name)
    {
        if (isset($this->$name)) {
            $val = $this->$name + 1;
            if ($val > 2) $val = 0;
            $this->$name = $val;
        }
    }

    public function resetForm()
    {
        $this->redirect(request()->header('Referer'));
    }

    private function onlyLocalhost(){
        if(!lfCheckLocalhost()){
            abort(403);
        }
    }
}

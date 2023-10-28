<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;

/**
 * Permissions trait
 */
trait HasPermissionsTrait
{
    /**
     * @var array
     */
    public $allPermissions = [];

    /**
     * @param ...$name
     * @return $this
     */
    public function givePermissionsTo(...$name)
    {

        $permissions = $this->getPermissions($name);
        if ($permissions === null) {
            return $this;
        }
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission->name)) {
                $this->permissions()->save($permission);
            }
        }

        return $this;
    }

    /**
     *
     * @param ...$name
     * @return $this
     */
    public function withdrawPermissionsTo(...$name)
    {

        $permissions = $this->getPermissions($name);
        $this->permissions()->detach($permissions);
        return $this;

    }

    /**
     * @param ...$permissions
     * @return \App\Models\User
     */
    public function refreshPermissions(...$permissions)
    {

        $this->permissions()->detach();
        return $this->givePermissionsTo(...$permissions);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        if(empty($this->allPermissions)){
            $this->getAllPermissions();
        }
       return in_array($permission,$this->allPermissions);
    }

    /**
     * @param $permission
     * @return bool
     */
    public function hasPermissionThroughRole($permission)
    {

        foreach ($permission->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ...$ids
     * @return void
     */
    public function refreshRoleIds(...$ids)
    {
        $this->roles()->detach();
        Role::find($ids)->map(function ($role) {
            $this->roles()->save($role);
        });
    }

    /**
     * @param ...$ids
     * @return bool
     */
    public function hasRoleIds(...$ids){
        // check super admins
        if($this->isSuperAdmin()){
            return true;
        }
        foreach ($ids as $role) {
            if ($this->roles->contains('id', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ...$roles
     * @return bool
     */
    public function hasRole(...$roles)
    {
        // check super admins
        if($this->isSuperAdmin()){
            return true;
        }

        foreach ($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {

        return $this->belongsToMany(Role::class, 'users_roles');

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {

        return $this->belongsToMany(Permission::class, 'users_permissions');

    }

    /**
     * @param $permission
     * @return bool
     */
    protected function hasPermission($permission)
    {

        return (bool)$this->permissions->where('name', $permission->name)->count();
    }

    /**
     * @param array $permissions
     * @return mixed
     */
    public function getAllPermissions()
    {
        $rt = $this->permissions()->get()->pluck("name","id")->toArray();
        $rt =$this->roles()->with("permissions")->get()->reduce(function ($rt,$item){
            $rt = $item->permissions->reduce(function ($rt,$it){
                $rt[$it->id] =$it->name;
                return $rt;
            },$rt);
            return $rt;
        },$rt);
        $this->allPermissions = $rt;
        return $rt;
    }


    /**
     * @param ...$name
     * @return mixed
     */
    protected function getPermissions(array $name){
        return Permission::whereIn('name',$name)->get();
    }
}

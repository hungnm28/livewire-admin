<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\StringCast;

class Permission extends Model
{
    use HasFactory;

    protected $table = 'permissions';

    protected $fillable = ["name", "label", "parent_id","type"];

    public static $listFields = ["id", "name", "label", "parent_id", "type" ,"created_at", "updated_at"];

    public function parent(){
        return $this->belongsTo(Permission::class,"parent_id","id");
    }
    public function children(){
        return $this->hasMany(Permission::class,"parent_id","id");
    }
     public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_permissions', "permission_id", "role_id", "id", "id");
    }

    protected $casts = [
        "name" => StringCast::class,
		"label" => StringCast::class,
		"type" => StringCast::class,

    ];
}

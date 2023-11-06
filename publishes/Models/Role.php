<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\StringCast;
use App\Casts\BooleanCast;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ["name"];

    public static $listFields = ["id", "name", "created_at", "updated_at"];

    public function permissions(){
        return $this->belongsToMany(Permission::class,'roles_permissions',"role_id","permission_id","id","id");
    }
    public function users() {

        return $this->belongsToMany(User::class,'users_roles');

    }
    protected $casts = [
        "name" => StringCast::class

    ];
}

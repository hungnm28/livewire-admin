<?php

namespace App\Models;
use App\Casts\BooleanCast;
use App\Casts\EmailCast;
use App\Casts\IntegerCast;
use App\Casts\StringCast;
use App\Traits\HasPermissionsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasPermissionsTrait;

    protected $table = 'users';

    protected $fillable = ["name", "email","password", "email_verified_at", "current_team_id", "profile_photo_path", "is_admin", "is_super_admin", "company_id", "department_id", "level", "position_id"];

    public static $listFields = ["id", "name", "email", "email_verified_at", "remember_token", "current_team_id", "profile_photo_path", "created_at", "updated_at", "is_admin", "is_super_admin", "company_id", "department_id", "level", "position_id"];
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        "name" => StringCast::class,
		"email" => EmailCast::class,
		"password" => StringCast::class,
		"remember_token" => StringCast::class,
		"profile_photo_path" => StringCast::class,
		"is_admin" => BooleanCast::class,
		"level" => IntegerCast::class,

    ];
}

<?php

namespace Hungnm28\LivewireAdmin\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    protected $signature = 'la:create-user';

    protected $description = 'Create User';

    public function handle()
    {
        $data['email'] = text("Email");
        $data['password'] = password("Password");
        $data['name'] = text("Name");
        $type = select('User Type',[
           'super_admin'=>'Super Admin',
           'admin'=>'Admin',
           'user'=>'User',
        ]);
        switch ($type){
            case 'super_admin':
                $data['is_admin'] = true;
                $data['is_super_admin'] = true;
                break;
            case 'admin':
                $data['is_admin'] = true;
                $data['is_super_admin'] = false;
                break;
            default:
                $data['is_admin'] = false;
                $data['is_super_admin'] = false;

        }

        $validate = Validator::make($data, [
            "email" => "email|required"
            , "password" => "string|required"
            , "name" => "string|required"
        ]);
        if ($validate->fails()) {
            $this->error(json_encode($validate->messages()));
            return false;
        }
        $data['password'] = Hash::make($data['password']);

        // CHeck user
        $user = User::whereEmail($data["email"])->first();
        if ($user) {
            $user->update($data);
        } else {
            User::create($data);
        }
        $this->info("Successful");
        return Command::SUCCESS;

    }

}
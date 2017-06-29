<?php

namespace TMPHP\RestApiGenerators\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AclGroupUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelsNamespace = config('rest-api-generator.namespaces.models');
        $authAclGroupUserModel = $modelsNamespace . '\AclGroupUser';
        $authGroupModel = $modelsNamespace . '\AclGroup';
        $userModel = $modelsNamespace . '\User';

        //find user admin
        $admin = $userModel
            ::where(['email' => config('rest-api-generator.admin_credentials.email')])
            ->first();

        //create user admin
        if ($admin === null) {
            $admin = $userModel::firstOrCreate(
                [
                    'name' => config('rest-api-generator.admin_credentials.name'),
                    'email' => config('rest-api-generator.admin_credentials.email'),
                    'password' => bcrypt(config('rest-api-generator.admin_credentials.password')),
                ]);
        }

        //create group "admin"
        $adminGroup = $authGroupModel::firstOrCreate(['name' => 'admin']);

        //assign to user group "admin"
        $authAclGroupUserModel::firstOrCreate([
            'group_id' => $adminGroup->id,
            'user_id' => $admin->id,
        ]);
    }
}

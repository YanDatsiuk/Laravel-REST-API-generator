<?php

namespace TMPHP\RestApiGenerators\Database\Seeds;

use Illuminate\Database\Seeder;

class AuthGroupUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelsNamespace = config('rest-api-generator.namespaces.models');
        $authAuthGroupUserModel = $modelsNamespace . '\AuthGroupUser';
        $authGroupModel = $modelsNamespace.'\AuthGroup';
        $userModel = $modelsNamespace . '\User';

        //create user admin
        $admin = $userModel::firstOrCreate(
            [
                'name' => 'John ADMIN',
                'email' => 'admin@gmail.com',//todo take from to config
                'password' => bcrypt('secret'),//todo take from to config
            ]);

        //create group "admin"
        $adminGroup = $authGroupModel::firstOrCreate(['name' => 'admin']);

        //assign to user group "admin"
        $authAuthGroupUserModel::firstOrCreate([
            'group_id' => $adminGroup->id,
            'user_id' => $admin->id,
        ]);
    }
}

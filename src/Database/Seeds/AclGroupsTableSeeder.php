<?php

namespace TMPHP\RestApiGenerators\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AclGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelsNamespace = config('rest-api-generator.namespaces.models');
        $authGroupModel = $modelsNamespace.'\AclGroup';

        $authGroupModel::firstOrCreate(['name' => 'guest']);
        $authGroupModel::firstOrCreate(['name' => 'registered']);
        $authGroupModel::firstOrCreate(['name' => 'admin']);
    }
}

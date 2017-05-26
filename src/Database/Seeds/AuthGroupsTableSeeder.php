<?php

namespace TMPHP\RestApiGenerators\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AuthGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelsNamespace = config('rest-api-generator.namespaces.models');
        $authGroupModel = $modelsNamespace.'\AuthGroup';

        $authGroupModel::firstOrCreate(['name' => 'guest']);
        $authGroupModel::firstOrCreate(['name' => 'registered']);
        $authGroupModel::firstOrCreate(['name' => 'admin']);
    }
}

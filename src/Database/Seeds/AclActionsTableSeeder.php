<?php

namespace TMPHP\RestApiGenerators\Database\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class AclActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**@var $api \Dingo\Api\Routing\Router* */
        $router = app('Dingo\Api\Routing\Router');

        //getting list of all routes
        $routeNames = [];
        foreach ($router->getRoutes() as $collection) {
            /**@var $collection \Dingo\Api\Routing\RouteCollection* */
            /**@var $route \Dingo\Api\Routing\Route* */
            foreach ($collection->getRoutes() as $route) {
                $routeNames[] = $route->getName();
            }
        }

        $modelsNamespace = config('rest-api-generator.namespaces.models');
        $authActionModel = $modelsNamespace.'\AclAction';

        //saving actions
        foreach ($routeNames as $routeName) {
            $authActionModel::firstOrCreate(['name' => $routeName]);
        }
    }
}

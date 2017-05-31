<?php

namespace TMPHP\RestApiGenerators;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use TMPHP\RestApiGenerators\Commands\IdeHelperCommand;
use TMPHP\RestApiGenerators\Commands\MakeAuthGroupsAndActionsCommand;
use TMPHP\RestApiGenerators\Commands\MakeCrudControllersCommand;
use TMPHP\RestApiGenerators\Commands\MakeCrudModelsCommand;
use TMPHP\RestApiGenerators\Commands\MakeCrudRoutesCommand;
use TMPHP\RestApiGenerators\Commands\MakeCrudTranformersCommand;
use TMPHP\RestApiGenerators\Commands\MakeRestApiProjectCommand;
use TMPHP\RestApiGenerators\Commands\MakeRestAuthCommand;
use TMPHP\RestApiGenerators\Commands\MakeSwaggerModelsCommand;
use TMPHP\RestApiGenerators\Commands\MakeSwaggerRootCommand;

/**
 * Class GeneratorsServiceProviders
 * @package TMPHP\RestApiGenerators
 */
class GeneratorsServiceProviders extends ServiceProvider
{
    /**
     * Bootstrap the application services. //testing commit
     *
     * @return void
     */
    public function boot()
    {
        //publishing configs
        $this->publishes(
            [
                __DIR__ . '/../config/rest-api-generator.php' => config_path('rest-api-generator.php'),
            ]
        );

        //register generated routes
        $apiRouteFilePath = base_path(config('rest-api-generator.paths.routes'). 'api.php');
        if (!$this->app->routesAreCached() && file_exists($apiRouteFilePath)) {
            require $apiRouteFilePath;
        }

        //register generated auth routes
        $authRouteFilePath = base_path(config('rest-api-generator.paths.routes'). 'auth.php');
        if (!$this->app->routesAreCached() && file_exists($authRouteFilePath)) {
            require $authRouteFilePath;
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            MakeCrudRoutesCommand::class,
            MakeSwaggerModelsCommand::class,
            MakeCrudModelsCommand::class,
            MakeCrudControllersCommand::class,
            MakeCrudTranformersCommand::class,
            MakeRestApiProjectCommand::class,
            MakeSwaggerRootCommand::class,
            MakeRestAuthCommand::class,
            MakeAuthGroupsAndActionsCommand::class,
            IdeHelperCommand::class,
        ]);
    }

}
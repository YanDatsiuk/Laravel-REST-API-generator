<?php

namespace TMPHP\RestApiGenerators;

use Illuminate\Support\ServiceProvider;
use TMPHP\RestApiGenerators\Commands\MakeCrudControllersCommand;
use TMPHP\RestApiGenerators\Commands\MakeCrudModelsCommand;
use TMPHP\RestApiGenerators\Commands\MakeCrudRoutesCommand;
use TMPHP\RestApiGenerators\Commands\MakeCrudTranformersCommand;
use TMPHP\RestApiGenerators\Commands\MakeRestApiProjectCommand;
use TMPHP\RestApiGenerators\Commands\MakeSwaggerModelsCommand;
use TMPHP\RestApiGenerators\Commands\MakeSwaggerRootCommand;

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
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerInstallCommand();
    }

    /**
     * Register the 'eternaltree:install' command.
     *
     * @return void
     */
    protected function registerInstallCommand()
    {
        $this->commands([
            MakeCrudRoutesCommand::class,
            MakeSwaggerModelsCommand::class,
            MakeCrudModelsCommand::class,
            MakeCrudControllersCommand::class,
            MakeCrudTranformersCommand::class,
            MakeRestApiProjectCommand::class,
            MakeSwaggerRootCommand::class,
        ]);
    }

}
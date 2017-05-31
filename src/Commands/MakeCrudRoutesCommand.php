<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use TMPHP\RestApiGenerators\Compilers\ApiRoutesCompiler;

/**
 * Class MakeCrudRoutesCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeCrudRoutesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:crud-routes 
                            {--models= : List of models, written as CSV in kebab notation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold a routes with REST API endpoints. Routes will be swagger documented.';


    /**
     * @var array list of model names.
     */
    private $models = [];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        //get list of models
        $this->models = explode(',', $this->option('models'));

        //check whether model names were submitted
        if (strlen($this->models[0]) === 0) {
            $this->error('Please specify model names in kebab notation');
            return;
        }

        //get saved before stub
        $apiRoutesCompiler = new ApiRoutesCompiler(
            null,
            null,
            (new ApiRoutesCompiler())->getSavedStub());


        //compile REST API routes for all models
        $apiRoutesCompiler->compile(['models' => $this->models]);

        $this->info('make:crud-routes cmd executed');
    }

}
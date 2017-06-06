<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use TMPHP\RestApiGenerators\Compilers\Core\CrudTransformerCompiler;

/**
 * Class MakeCrudTransformersCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeCrudTransformersCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:crud-transformers 
                            {--models= : List of models, written as CSV in camelCase notation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold transformers for REST API.';


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
            $this->error('Please specify model names in camelCase notation');
            return;
        }

        //compile transformers for all models
        foreach ($this->models as $model) {

            $crudTransformerCompiler = new CrudTransformerCompiler();
            $crudTransformerCompiler->compile([
                'modelNameCamelcase' => $model,
            ]);
        }

        $this->info('make:crud-transformers  cmd executed');
    }

}
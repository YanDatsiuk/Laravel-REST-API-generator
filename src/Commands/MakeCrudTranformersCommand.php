<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use TMPHP\RestApiGenerators\Compilers\CrudTransformerCompiler;

class MakeCrudTranformersCommand extends Command
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
    protected $description = 'Create transformers for CRUD REST API.';


    /**
     * @var array list of model names
     */
    private $models = [];

    /**
     * Execute the console command.
     *
     * @return mixed
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

        //generate CRUD controllers for all models
        foreach ($this->models as $model) {
            $crudTransformerCompiler = new CrudTransformerCompiler();
            $crudTransformerCompiler->compile([
                'modelNameCamelcase' => $model
            ]);
        }

        //
        $this->info('make:crud-transformers  cmd executed');

    }

}
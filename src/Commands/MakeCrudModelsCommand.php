<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use TMPHP\RestApiGenerators\Compilers\Models\CrudModelCompiler;

/**
 * Class MakeCrudModelsCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeCrudModelsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:crud-models 
                            {--models= : List of models, written as CSV in camelCase notation}
                            {--tables= : List of tables, written as CSV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold CRUD models for REST API.';

    /**
     * @var array list of model names.
     */
    private $models = [];

    /**
     * @var array list of table names for gathering schema info.
     */
    private $tables = [];


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        //get list of models
        $this->models = explode(',', $this->option('models'));

        //get list of database tables
        $this->tables = explode(',', $this->option('tables'));

        //check whether model names were submitted
        if (strlen($this->models[0]) === 0) {
            $this->error('Please specify model names in camelCase notation');
            return;
        }

        //check whether model names were submitted
        if (strlen($this->tables[0]) === 0) {
            $this->error('Please specify table names');
            return;
        }

        //check whether table quantity are equal to model names quantity
        if (count($this->models) !== count($this->tables)) {
            $this->error('table names quantity are not equal to model names quantity');
            return;
        }

        //compile swagger models
        for ($i = 0; $i < count($this->models); $i++) {
            $crudModelCompiler = new CrudModelCompiler();
            $crudModelCompiler->compile([
                'modelName' => $this->models[$i],
                'tableName' => $this->tables[$i],
            ]);
        }

        $this->info('make:crud-models cmd executed');
    }

}
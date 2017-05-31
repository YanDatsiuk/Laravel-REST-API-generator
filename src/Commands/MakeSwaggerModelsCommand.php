<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use TMPHP\RestApiGenerators\Compilers\SwaggerDefinitionCompiler;

/**
 * Class MakeSwaggerModelsCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeSwaggerModelsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:swagger-models 
                            {--models= : List of models, written as CSV in kebab notation.}
                            {--tables= : List of tables, written as CSV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold swagger models.';

    /**
     * List of model names.
     *
     * @var array
     */
    private $models = [];

    /**
     * List of table names for gathering schema info.
     *
     * @var array
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

        //validate input data
        if (!$this->isInputValid($this->models, $this->tables)){
            return;
        }

        //compile swagger models
        for ($i = 0; $i < count($this->models); $i++) {
            $swaggerDefinitionCompiler = new SwaggerDefinitionCompiler();
            $swaggerDefinitionCompiler->compile([
                'modelName' => $this->models[$i],
                'tableName' => $this->tables[$i],
            ]);
        }

        $this->info('make:swagger-models cmd executed.');
    }

    /**
     * Validate input data.
     *
     * @param array $models
     * @param array $tables
     * @return bool
     */
    private function isInputValid(array $models, array $tables)
    {
        //check whether model names were submitted
        if (strlen($models[0]) === 0) {
            $this->error('Please specify model names in kebab notation.');
            return false;
        }

        //check whether model names were submitted
        if (strlen($tables[0]) === 0) {
            $this->error('Please specify table names.');
            return false;
        }

        //check whether table quantity are equal to model names quantity
        if (count($models) !== count($tables)) {
            $this->error('Table names quantity are not equal to model names quantity.');
            return false;
        }

        return true;
    }
}
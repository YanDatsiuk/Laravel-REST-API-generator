<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use TMPHP\RestApiGenerators\Support\Helper;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

/**
 * Class MakeRestApiProjectCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeRestApiProjectCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:rest-api-project
                            {--models= : List of models, written as CSV in kebab notation}}
                            {--tables= : List of tables, written as CSV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create REST API project.';

    /**
     * List of model names
     *
     * @var array
     */
    private $models = [];

    /**
     * CSV - models in kebab notation
     *
     * @var string
     */
    private $modelsInKebabNotaion;

    /**
     * CSV - models in camel case notation
     *
     * @var string
     */
    private $modelsInCamelCaseNotation;

    /**
     * List of table names for gathering schema info
     *
     * @var array
     */
    private $tables = [];

    /** @var AbstractSchemaManager */
    private $schema;


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        //initialize submitted parameters and stop execution if there are any errors
        $isValidInput = $this->initInputParams();

        if (!$isValidInput) {

            $this->warn('You do not pass --models and --tables parameters');

            $choice = $this->choice('What to do next?', [
                '0. Take models and tables list from configuration file.',
                '1. Use default convention and take info from db schema.',
            ]);
            $choice = substr($choice, 0, 1);

            switch ($choice) {
                case "0":
                    $isValidConfig = $this->loadParametersFromConfigFile();
                    if (!$isValidConfig) {
                        $this->error('wrong config');

                        return;
                    }
                    break;
                case "1":
                    $isValidConfig = $this->loadParametersFromDatabaseSchema();
                    if (!$isValidConfig) {
                        $this->error('wrong config');

                        return;
                    }
                    break;
                default:
                    return;
                    break;
            }
        }

        //call artisan commands for generating models, transformers, controllers, swagger-docs and routes
        $this->callGenerators();

        $this->info('All files for REST API project were generated!');
        $this->info('Please see all files in /storage/CRUD directory');
    }

    /** Initialize submitted parameters or read them from configuration file */
    private function initInputParams()
    {

        //get list of models
        $this->models = explode(',', $this->option('models'));

        //get list of database tables
        $this->tables = explode(',', $this->option('tables'));

        //check whether model names were submitted
        if (strlen($this->models[0]) === 0) {
            $this->warn('Please specify model names in kebab notation');

            return false;
        }

        //check whether model names were submitted
        if (strlen($this->tables[0]) === 0) {
            $this->warn('Please specify table names');

            return false;
        }

        //check whether table quantity are equal to model names quantity
        if (count($this->models) !== count($this->tables)) {
            $this->error('table names quantity are not equal to model names quantity');

            return false;
        }

        $this->transformModelsToRequiredNotations();

        return true;
    }

    /** Transform model array to string variables with different notations */
    private function transformModelsToRequiredNotations()
    {
        $this->modelsInKebabNotaion = implode(',', $this->models);

        //transform model names from kebab to camelCase notation
        $_modelsInCamelCaseNotation = [];

        foreach ($this->models as $model) {
            array_push($_modelsInCamelCaseNotation, $this->kebabToCamelCase($model));
        }

        $this->modelsInCamelCaseNotation = implode(',', $_modelsInCamelCaseNotation);
    }

    /**
     * Convert string in kebab notation to camelCase notation
     *
     * @param string $string
     *
     * @return string
     */
    private function kebabToCamelCase(string $string): string
    {
        $_modelInCamelCaseNotation = '';

        foreach (explode('-', $string) as $kebabStringPart) {
            $_modelInCamelCaseNotation .= ucfirst($kebabStringPart);
        }

        return $_modelInCamelCaseNotation;
    }

    /** Load parameters from configuration file. */
    private function loadParametersFromConfigFile()
    {
        $modelNamesTables = config('rest-api-generator.models');
        $this->models = array_keys($modelNamesTables);
        $this->tables = array_values($modelNamesTables);

        $this->transformModelsToRequiredNotations();

        return true;
    }

    /** Load parameters from database schema, using Doctrine Schema Manager */
    private function loadParametersFromDatabaseSchema()
    {
        $this->schema = DB::getDoctrineSchemaManager();
        $this->tables = $this->schema->listTableNames();

        //remove excluded tables from generation process
        $excludedTables = config('rest-api-generator.excluded_tables');
        $this->tables = array_diff($this->tables, $excludedTables);

        $dbTablePrefix = config('rest-api-generator.db_table_prefix');
        $this->models = Helper::getModelNamesFromTableNames($this->tables, $dbTablePrefix);

        $this->transformModelsToRequiredNotations();

        return true;
    }

    /**
     * Call artisan commands for generating models, transformers, controllers, swagger-docs and routes
     */
    private function callGenerators()
    {
        //create CRUD models.
        Artisan::call('make:crud-models', [
            '--models' => $this->modelsInCamelCaseNotation,
            '--tables' => implode(',', $this->tables),
        ]);

        //create transformers for CRUD REST API.
        Artisan::call('make:crud-transformers', [
            '--models' => $this->modelsInCamelCaseNotation,
        ]);

        //Create controllers for CRUD REST API.
        Artisan::call('make:crud-controllers', [
            '--models' => $this->modelsInCamelCaseNotation,
        ]);

        //php artisan make:swagger-models
        Artisan::call('make:swagger-models', [
            '--models' => $this->modelsInKebabNotaion,
            '--tables' => implode(',', $this->tables),
        ]);

        //php artisan make:crud-routes
        Artisan::call('make:crud-routes', [
            '--models' => $this->modelsInKebabNotaion,
        ]);

        //php artisan make:crud-routes
        Artisan::call('make:swagger-root');

        //php artisan migrate:generate --no-interaction
        Artisan::call('migrate:generate', ['--no-interaction' => true]);
    }

}
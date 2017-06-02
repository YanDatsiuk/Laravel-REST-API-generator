<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\Support\Helper;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use TMPHP\RestApiGenerators\Support\SchemaManager;
use Xethron\MigrationsGenerator\MigrateGenerateCommand;

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
                            {silent-mode? : Whether all input should be default}
                            {--models= : List of models, written as CSV in kebab notation}
                            {--tables= : List of tables, written as CSV}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold REST API project.';

    /**
     * List of model names.
     *
     * @var array
     */
    private $modelNames = [];

    /**
     * CSV - models in kebab notation.
     *
     * @var string
     */
    private $modelsInKebabNotation;

    /**
     * CSV - models in camel case notation.
     *
     * @var string
     */
    private $modelsInCamelCaseNotation;

    /**
     * List of table names for gathering schema info.
     *
     * @var array
     */
    private $tableNames = [];

    /** @var AbstractSchemaManager */
    private $schema;

    /**
     * Set a list of tables,
     * for which require to generate migrations.
     *
     * @var array list of the table names.
     */
    private $tablesForMigrationGeneration = [];

    /**
     * @var bool
     */
    private $silentMode = false;


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        //
        $this->schema = new SchemaManager();

        //initialize submitted parameters and stop execution if there are any errors
        $isValidInput = $this->initInputParams();

        if (!$isValidInput) {

            $this->warn('You do not pass --models and --tables parameters');

            $this->choicesOnAbsentOptions();

            //transform model array to string variables with different required notations
            $this->transformModelsToRequiredNotations();
        }

        //set a list of required migrations for tables
        $this->setListOfRequiredMigrations();

        //call artisan commands for generating models, transformers, controllers, swagger-docs and routes
        $this->callGenerators();

        $this->info('All files for REST API project were generated!');
        $this->info('Please see all files in /storage/CRUD directory');
    }

    /** Initialize submitted parameters or read them from configuration file. */
    private function initInputParams()
    {
        //check "silent-mode" argument
        if ($this->argument('silent-mode')) {
            $this->silentMode = true;
            $this->info('all user input will be default.');
        }

        //get list of models
        $this->modelNames = explode(',', $this->option('models'));

        //get list of database tables
        $this->tableNames = explode(',', $this->option('tables'));

        //check whether model names were submitted
        if (strlen($this->modelNames[0]) === 0) {
            $this->warn('Please specify model names in kebab notation');

            return false;
        }

        //check whether model names were submitted
        if (strlen($this->tableNames[0]) === 0) {
            $this->warn('Please specify table names');

            return false;
        }

        //check whether table quantity are equal to model names quantity
        if (count($this->modelNames) !== count($this->tableNames)) {
            $this->error('table names quantity are not equal to model names quantity');

            return false;
        }

        return true;
    }

    /**
     * Show choices to programmer,
     * if there are not any options ("models" and "tables") provided with this command.
     */
    private function choicesOnAbsentOptions()
    {
        //set default choice
        $choice = "1";

        //get input from user if command isn't in silent mode
        if (!$this->silentMode) {
            $choice = $this->choice('What to do next?', [
                '0. Take models and tables list from configuration file.',
                '1. Generate code for ALL database tables.',
            ], '1');
            $choice = substr($choice, 0, 1);
        }

        //load list of models and tables from config file or use all tables from schema
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

    /** Load list of models and tables from configuration file. */
    private function loadParametersFromConfigFile()
    {
        $modelNamesTables = config('rest-api-generator.models');
        $this->modelNames = array_keys($modelNamesTables);
        $this->tableNames = array_values($modelNamesTables);

        return true;
    }

    /** Load all models and tables from database schema, using Doctrine Schema Manager. */
    private function loadParametersFromDatabaseSchema()
    {
        //get all tables from database schema
        $this->tableNames = $this->schema->listTableNames();

        //remove excluded tables from generation process
        $excludedTables = config('rest-api-generator.excluded_tables');
        $this->tableNames = array_diff($this->tableNames, $excludedTables);

        $dbTablePrefix = config('rest-api-generator.db_table_prefix');
        $this->modelNames = Helper::getModelNamesFromTableNames($this->tableNames, $dbTablePrefix);

        return true;
    }

    /** Transform model array to string variables with different notations. */
    private function transformModelsToRequiredNotations()
    {
        $this->modelsInKebabNotation = implode(',', $this->modelNames);

        //transform model names from kebab to camelCase notation
        $_modelsInCamelCaseNotation = [];

        foreach ($this->modelNames as $model) {
            array_push($_modelsInCamelCaseNotation, Helper::kebabToCamelCase($model));
        }

        $this->modelsInCamelCaseNotation = implode(',', $_modelsInCamelCaseNotation);
    }

    /**
     * Set list of tables with missed migration files.
     */
    private function setListOfRequiredMigrations()
    {
        //get list of all table names
        $allTableNames = $this->schema->listTableNames();

        //get list of all existing migrations
        $migrationFiles = scandir(database_path('migrations'));

        //set list of tables with missed migration files
        $this->tablesForMigrationGeneration = array_filter($allTableNames, function ($tableName) use ($migrationFiles) {
            foreach ($migrationFiles as $migrationFile) {
                if (str_contains($migrationFile, $tableName)) {
                    return false;
                }
            }
            return true;
        });
    }

    /**
     * Call artisan commands for generating models, transformers, controllers, swagger-docs and routes.
     */
    private function callGenerators()
    {
        //scaffold api for image management
        if ($this->silentMode || $this->confirm('Generate API for image management?', true)) {
            Artisan::call('make:image-manager-api', [], $this->output);
        }

        //scaffold models
        Artisan::call('make:crud-models', [
            '--models' => $this->modelsInCamelCaseNotation,
            '--tables' => implode(',', $this->tableNames),
        ]);

        //scaffold transformers
        Artisan::call('make:crud-transformers', [
            '--models' => $this->modelsInCamelCaseNotation,
        ]);

        //scaffold controllers
        Artisan::call('make:crud-controllers', [
            '--models' => $this->modelsInCamelCaseNotation,
        ]);

        //scaffold swagger models
        Artisan::call('make:swagger-models', [
            '--models' => $this->modelsInKebabNotation,
            '--tables' => implode(',', $this->tableNames),
        ]);

        //scaffold routes
        Artisan::call('make:crud-routes', [
            '--models' => $this->modelsInKebabNotation,
        ]);

        //scaffold swagger root
        Artisan::call('make:swagger-root');

        //scaffold authentication code
        if ($this->silentMode || $this->confirm('Generate AUTH code?', true)) {
            Artisan::call('make:rest-auth', [], $this->output);
        }

        //generate migrations for database schema
        if ($this->tablesForMigrationGeneration) {
            Artisan::call('migrate:generate',
                [
                    'tables' => implode(',', $this->tablesForMigrationGeneration),
                    '--no-interaction' => true
                ]);
        }

        //generate ide helper documentation
        if ($this->silentMode || $this->confirm('Generate ide helper documentation?', true)) {
            Artisan::call('ide-helper:all', [], $this->output);
        }

    }

}
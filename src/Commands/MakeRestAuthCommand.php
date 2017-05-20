<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use TMPHP\RestApiGenerators\Compilers\ApiRoutesCompiler;
use TMPHP\RestApiGenerators\Compilers\AuthControllerCompiler;
use TMPHP\RestApiGenerators\Compilers\AuthRoutesCompiler;
use TMPHP\RestApiGenerators\Compilers\ForgotPasswordControllerCompiler;
use TMPHP\RestApiGenerators\Compilers\LoginDefinitionCompiler;
use TMPHP\RestApiGenerators\Compilers\RegisterDefinitionCompiler;
use TMPHP\RestApiGenerators\Compilers\ResetDefinitionCompiler;
use TMPHP\RestApiGenerators\Compilers\ResetLinkRequestDefinitionCompiler;
use TMPHP\RestApiGenerators\Compilers\ResetPasswordControllerCompiler;
use TMPHP\RestApiGenerators\Support\SchemaManager;

/**
 * Class MakeRestAuthCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeRestAuthCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:rest-auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create REST API authentication code.';

    /**
     * @var SchemaManager
     */
    private $schema;

    /**
     * MakeRestAuthCommand constructor.
     * @param \Illuminate\Console\OutputStyle|null $output
     */
    public function __construct(OutputStyle $output = null)
    {
        if ($output !== null){
            $this->output = $output;
        }

        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        //
        $this->schema = new SchemaManager();

        //check default tables existence
        if ($this->existsDefaultAuthTables()) {

            $this->makeRestAuth();
        } else {

            $this->alert('No auth tables exist.');

            $this->choicesOnAbsentAuthTables();
        }
    }

    /**
     * Show choices to programmer,
     * if there are not any AUTH tables in database schema.
     */
    private function choicesOnAbsentAuthTables()
    {
        $choice = $this->choice('Migrate auth tables into database schema?', [
            '0. Yes.',
            '1. No.',
        ]);
        $choice = substr($choice, 0, 1);

        switch ($choice) {

            case "0":
                $this->migrateAuthTables();
                $this->makeRestAuth();
                $this->info('Auth code was generated.');
                break;

            case "1":
                $this->alert('No auth code was generated.');
                break;

            default:
                $this->alert('No auth code was generated.');
                break;
        }
    }

    /**
     * Make REST API authentication
     */
    private function makeRestAuth()
    {
        //compile auth controllers and save them in the controllers path
        $this->compileAuthControllers();

        //compile auth swagger definitions
        $this->compileAuthSwaggerDefinitions();

        //scaffold AUTH groups and actions code
        $makeAuthGroupsAndActionsCommand = new MakeAuthGroupsAndActionsCommand($this->output);
        $makeAuthGroupsAndActionsCommand->fire();

        //append auth routes to routes/api.php
        $this->appendAuthRoutes();

        $this->info('All files for REST API authentication code were generated!');
    }

    /**
     * Compile auth controllers and save them in the controllers path
     */
    private function compileAuthControllers()
    {
        $authControllerCompiler = new AuthControllerCompiler();
        $authControllerCompiler->compile([]);

        $forgotPasswordControllerCompiler = new ForgotPasswordControllerCompiler();
        $forgotPasswordControllerCompiler->compile([]);

        $resetPasswordControllerCompiler = new ResetPasswordControllerCompiler();
        $resetPasswordControllerCompiler->compile([]);
    }

    /**
     * Compile auth swagger definitions
     */
    private function compileAuthSwaggerDefinitions()
    {
        //compile login definition
        $loginDefinitionCompiler = new LoginDefinitionCompiler();
        $loginDefinitionCompiler->compile([]);

        //compile register definition
        $registerDefinitionCompiler = new RegisterDefinitionCompiler();
        $registerDefinitionCompiler->compile([]);

        //compile reset link request definition
        $resetLinkRequestDefinitionCompiler = new ResetLinkRequestDefinitionCompiler();
        $resetLinkRequestDefinitionCompiler->compile([]);

        //compile reset definition
        $resetDefinitionCompiler = new ResetDefinitionCompiler();
        $resetDefinitionCompiler->compile([]);

    }

    /**
     * Append auth routes to routes/api.php
     */
    private function appendAuthRoutes(): void
    {
        //compile auth routes
        $authRoutesCompiler = new AuthRoutesCompiler();
        $authRoutesCompiler->compile([]);
    }

    /**
     * Check default tables ("users" and "password_resets") existence.
     *
     * @return bool
     */
    private function existsDefaultAuthTables()
    {
        return $this->schema->existsTables(['users', 'password_resets']);
    }

    /**
     * Create missed "users" and "password_resets" tables in the database schema.
     */
    private function migrateAuthTables()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('remember_token', 100)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('password_resets')) {
            Schema::create('password_resets', function (Blueprint $table) {
                $table->string('email')->index();
                $table->string('token');
                $table->dateTime('created_at')->nullable();
            });
        }
    }

}
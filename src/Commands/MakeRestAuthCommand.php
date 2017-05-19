<?php

namespace TMPHP\RestApiGenerators\Commands;


use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use TMPHP\RestApiGenerators\Compilers\AuthControllerCompiler;
use TMPHP\RestApiGenerators\Compilers\AuthRoutesCompiler;
use TMPHP\RestApiGenerators\Compilers\ForgotPasswordControllerCompiler;
use TMPHP\RestApiGenerators\Compilers\LoginDefinitionCompiler;
use TMPHP\RestApiGenerators\Compilers\RegisterDefinitionCompiler;
use TMPHP\RestApiGenerators\Compilers\ResetPasswordControllerCompiler;

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
     * @var AbstractSchemaManager
     */
    private $schema;


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        //
        $this->schema = DB::getDoctrineSchemaManager();

        //compile auth controllers and save them in the controllers path
        $this->compileAuthControllers();

        //append auth routes to routes/api.php
        $this->appendAuthRoutes();

        //compile auth swagger definitions
        $this->compileAuthSwaggerDefinitions();

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
    }

    /**
     * Append auth routes to routes/api.php
     */
    private function appendAuthRoutes(): void
    {
        //todo add validation whether rotes/api.php file exists

        $apiRoutesPath = base_path(config('rest-api-generator.paths.routes'));
        $apiRoutesFileName = $apiRoutesPath . 'api.php';
        $apiRoutesFile = file_get_contents($apiRoutesFileName);

        //compile auth routes
        $authRoutesCompiler = new AuthRoutesCompiler();
        $authRoutes = $authRoutesCompiler->compile([]);

        if (str_contains($apiRoutesFile, 'Auth Routes')) {
            $this->alert('There is already auth routes in your routes file');
        } else {
            file_put_contents($apiRoutesFileName, "\n\n" . $authRoutes . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

}
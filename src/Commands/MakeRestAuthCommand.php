<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use TMPHP\RestApiGenerators\Compilers\AuthControllerCompiler;
use TMPHP\RestApiGenerators\Compilers\ForgotPasswordControllerCompiler;
use TMPHP\RestApiGenerators\Compilers\ResetPasswordControllerCompiler;
use TMPHP\RestApiGenerators\Support\Helper;
use Doctrine\DBAL\Schema\AbstractSchemaManager;

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
        $this->schema = DB::getDoctrineSchemaManager();

        $authControllerComipler = new AuthControllerCompiler();
        $authControllerComipler->compile([]);

        $forgotPasswordControllerCompiler = new ForgotPasswordControllerCompiler();
        $forgotPasswordControllerCompiler->compile([]);


        $resetPasswordControllerCompiler = new ResetPasswordControllerCompiler();
        $resetPasswordControllerCompiler->compile([]);

        $this->info('All files for REST API authentication code were generated!');
    }

}
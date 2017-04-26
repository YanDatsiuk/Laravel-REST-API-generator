<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use TMPHP\RestApiGenerators\Compilers\SwaggerRootCompiler;
use TMPHP\RestApiGenerators\Support\Helper;

/**
 * Class MakeSwaggerRootCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeSwaggerRootCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:swagger-root';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate root object for swagger documentation.';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $swaggerRootCompiler = new SwaggerRootCompiler();

        $host = Helper::trimProtocolFromUrl(env('APP_URL'));

        $swaggerRootCompiler->compile(['Host' => $host]);

        $this->info('make:swagger-root cmd executed '. $host);
    }

}
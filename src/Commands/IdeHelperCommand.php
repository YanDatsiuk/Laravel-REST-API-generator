<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use TMPHP\RestApiGenerators\Compilers\CrudModelCompiler;

/**
 * Class IdeHelperCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class IdeHelperCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'ide-helper:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all ide-helper commands.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {

        $this->info('All ide-helper commands executed');
    }

}
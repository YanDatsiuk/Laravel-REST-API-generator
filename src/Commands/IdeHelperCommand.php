<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;


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
        Artisan::call('clear-compiled', [], $this->output);
        Artisan::call('ide-helper:generate', [], $this->output);
        Artisan::call('optimize', [], $this->output);
        Artisan::call('ide-helper:models', [], $this->output);
        Artisan::call('ide-helper:meta', [], $this->output);

        $this->info('All ide-helper commands executed');
    }

}
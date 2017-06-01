<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use TMPHP\RestApiGenerators\Compilers\CrudModelCompiler;

/**
 * Class MakeImageManagerCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeImageManagerCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:image-manager-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold api for image management.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        //todo run migrations
        //todo generate models, controllers, definitions and transformers
        //todo compile image management routes

        $this->info('make:image-manager-api command executed');
    }

    /**
     * Create missed tables in the database schema.
     * ('images', 'auth_groups', 'auth_action_group', 'auth_group_user')
     */
    private function migrateRequiredTables()
    {
        if (!Schema::hasTable('images')) {
            Schema::create('images', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('image_src');
                $table->timestamps();
            });
        }

    }

}
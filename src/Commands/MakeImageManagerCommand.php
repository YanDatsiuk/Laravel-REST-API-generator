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
        //run migrations
        $this->migrateRequiredTables();

        //generate models, controllers, definitions and transformers
        $this->scaffoldCode();

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

    private function scaffoldCode()
    {
        //scaffold models
        Artisan::call('make:crud-models', [
            '--models' => 'Image',
            '--tables' => 'images',
        ]);

        //scaffold transformers
        Artisan::call('make:crud-transformers', [
            '--models' => 'Image',
        ]);

        //scaffold controllers todo compile ImageControllerCompiler
//        Artisan::call('make:crud-controllers', [
//            '--models' => 'Image',
//        ]);

        //scaffold swagger models
        Artisan::call('make:swagger-models', [
            '--models' => 'image',
            '--tables' => 'images',
        ]);

        $this->info('All code for image management generated!');
    }

}
<?php

namespace TMPHP\RestApiGenerators\Commands;


use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use TMPHP\RestApiGenerators\Support\SchemaManager;

/**
 * Class MakeAuthGroupsAndActionsCommand
 * @package TMPHP\RestApiGenerators\Commands
 */
class MakeAuthGroupsAndActionsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:auth-groups-and-actions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create groups and actions for AUTH.';

    /**
     * @var SchemaManager
     */
    private $schema;

    /**
     * MakeAuthGroupsAndActionsCommand constructor.
     * @param OutputStyle|null $output
     */
    public function __construct(OutputStyle $output = null)
    {
        if ($output !== null) {
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
        if ($this->existsRequiredTables()) {

            $this->makeAclGroupsAndActions();
        } else {

            $this->alert('No required exist.');

            $this->choicesOnAbsentRequiredTables();
        }
    }

    /**
     * Show choices to programmer,
     * if there are not any required tables in database schema.
     */
    private function choicesOnAbsentRequiredTables()
    {
        $choice = $this->choice('Migrate required tables into database schema?', [
            '0. Yes.',
            '1. No.',
        ]);
        $choice = substr($choice, 0, 1);

        switch ($choice) {

            case "0":
                $this->migrateRequiredTables();
                $this->makeAclGroupsAndActions();
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
     *
     */
    private function makeAclGroupsAndActions()
    {
        //scaffold models
        Artisan::call('make:crud-models', [
            '--models' => 'User,AuthAction,AuthGroup,AuthActionGroup,AuthGroupUser',
            '--tables' => 'users,acl_actions,acl_groups,acl_action_group,acl_group_user',
        ]);

        //scaffold transformers
        Artisan::call('make:crud-transformers', [
            '--models' => 'User,AuthAction,AuthGroup,AuthActionGroup,AuthGroupUser',
        ]);

        //scaffold controllers
        Artisan::call('make:crud-controllers', [
            '--models' => 'User,AuthAction,AuthGroup,AuthActionGroup,AuthGroupUser',
        ]);

        //scaffold swagger models
        Artisan::call('make:swagger-models', [
            '--models' => 'user,auth-action,auth-group,auth-action-group,auth-group-user',
            '--tables' => 'users,acl_actions,acl_groups,acl_action_group,acl_group_user',
        ]);

        //scaffold routes
        Artisan::call('make:crud-routes', [
            '--models' => 'user,auth-action,auth-group,auth-action-group,auth-group-user',
        ]);

        $this->info('All groups and actions for AUTH code were generated!');
    }

    /**
     * Check tables existence.
     * ("acl_actions", "acl_groups", "acl_action_group", "acl_group_user")
     *
     * @return bool
     */
    private function existsRequiredTables()
    {
        return $this->schema->existsTables([
            'users',
            'acl_actions',
            'acl_groups',
            'acl_action_group',
            'acl_group_user'
        ]);
    }

    /**
     * Create missed tables in the database schema.
     * ('acl_actions', 'acl_groups', 'acl_action_group', 'acl_group_user')
     * TODO refactor possible: 1. Move migration code into the separate files. 2. Create folder "migrations".
     * TODO 3. Check schema from here, but run migrations from outer files.
     */
    private function migrateRequiredTables()
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

        if (!Schema::hasTable('acl_actions')) {
            Schema::create('acl_actions', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('name', 1024)->nullable();
            });
        }

        if (!Schema::hasTable('acl_groups')) {
            Schema::create('acl_groups', function(Blueprint $table)
            {
                $table->increments('id');
                $table->string('name', 1024)->nullable();
            });
        }

        if (!Schema::hasTable('acl_action_group')) {
            Schema::create('acl_action_group', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('action_id')->unsigned()->nullable()->index('index2');
                $table->integer('group_id')->unsigned()->nullable()->index('index3');
            });
            Schema::table('acl_action_group', function(Blueprint $table)
            {
                $table->foreign('action_id', 'fk_acl_action_group_1')->references('id')->on('acl_actions')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign('group_id', 'fk_acl_action_group_2')->references('id')->on('acl_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }

        if (!Schema::hasTable('acl_group_user')) {
            Schema::create('acl_group_user', function(Blueprint $table)
            {
                $table->increments('id');
                $table->integer('group_id')->unsigned()->nullable()->index('index2');
                $table->integer('user_id')->unsigned()->nullable()->index('index3');
            });
            Schema::table('acl_group_user', function(Blueprint $table)
            {
                $table->foreign('group_id', 'fk_acl_group_user_1')->references('id')->on('acl_groups')->onUpdate('CASCADE')->onDelete('CASCADE');
                $table->foreign('user_id', 'fk_acl_group_user_2')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            });
        }
    }

}
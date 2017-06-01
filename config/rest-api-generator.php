<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    |
    | This value is the namespace of the generated classes. Namespace is a set
    | of symbols that are used to organize objects of various kinds, so that
    | these objects may be referred to by name.
    */
    'namespaces' => [
        'controllers' => 'App\REST\Http\Controllers\Api\v1',
        'models' => 'App\REST',
        'transformers' => 'App\REST\Transformers'
    ],

    /*
    |--------------------------------------------------------------------------
    | Paths
    |--------------------------------------------------------------------------
    |
    | This value is the paths of the generated files.
    */
    'paths' => [
        'controllers' => '/app/REST/Http/Controllers/Api/V1/',
        'models' => '/app/REST/',
        'transformers' => '/app/REST/Transformers/',
        'documentations' => '/app/REST/Documentations/',
        'routes' => '/app/REST/routes/',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    | 
    | This is an array of model configurations. Where key is the name of
    | the model and the value of a table that will use this model. These
    | settings are relevant only if the method is used to read data from the
    | configuration file.
    */
    'models' => [
        'user' => 'tb_users',
    ],

    /*
    |--------------------------------------------------------------------------
    | DB table prefix
    |--------------------------------------------------------------------------
    |
    | This value is the prefix of db-tables. This value will be used only if 
    | the script for reading the database schema is selected
    | during the generation. This prefix will be excluded from
    | the generated data (class name, namespace, etc.), but will be taken into account
    | when defining the table for the model
    */
    'db_table_prefix' => 'tb_',

    /**
     * This is a list of tables,
     * which will not take a part in generation API process.
     * For them WILL NOT be generated: models, controllers, routes, e.t.c...
     */
    'excluded_tables' => ['migrations', 'password_resets', 'images'],

    /**
     * This is a credentials for super admin user.
     * This user entity will be seeded.
     */
    'admin_credentials' => [
        'name' => 'SUPER ADMIN',
        'email' => 'admin@gmail.com',
        'password' => 'secret',
    ]
];
Laravel REST API Generator
==========================

Code scaffolding for REST API project.

Installation
============

Open your `config/app.php` and add this line in `providers` section
```php
    \TMPHP\RestApiGenerators\GeneratorsServiceProviders::class,
    Dingo\Api\Provider\LaravelServiceProvider::class,
    Way\Generators\GeneratorsServiceProvider::class,
    Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class,
    Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class,
    \L5Swagger\L5SwaggerServiceProvider::class,
```

Publishing configuration files
==============================

Execute command
```php
php artisan vendor:publish
```

Generating code
===============

Execute command
```php
php artisan make:rest-api-project
```

Notes
=====
Make sure, that you have created proper database schema.

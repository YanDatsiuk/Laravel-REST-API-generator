[![Codacy Badge](https://api.codacy.com/project/badge/Grade/bdc684fc4dc84708a4cd201ae70499a6)](https://www.codacy.com/app/YanDatsyuk/Laravel-REST-API-generator?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=YanDatsyuk/Laravel-REST-API-generator&amp;utm_campaign=Badge_Grade)

Laravel REST API Generator
==========================

Code scaffolding for REST API project. 
This package is available also on [packagist](https://packagist.org/packages/tmphp/rest-api-generators). And installation via packagist is preferred way.

## Installation

### Package installation

* add `"tmphp/rest-api-generators": "dev-master"` to your composer.json (node `"require-dev"`)
* run `php artisan composer update`

### Configuration

Open your `config/app.php` and add this line in `providers` section
```php
    TMPHP\RestApiGenerators\GeneratorsServiceProviders::class,
    Dingo\Api\Provider\LaravelServiceProvider::class,
    Way\Generators\GeneratorsServiceProvider::class,
    Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider::class,
    Tymon\JWTAuth\Providers\JWTAuthServiceProvider::class,
    L5Swagger\L5SwaggerServiceProvider::class,
    Abhijitghogre\LaravelDbClearCommand\LaravelDbClearCommandServiceProvider::class,
    Felixkiss\UniqueWithValidator\ServiceProvider::class,
```

### Publishing configuration files

Execute command
```php
php artisan vendor:publish
```

### Database schema

Make sure, that you have created database schema. 
For generating relations you should have FOREIGN KEY Constraints.

### Configurating .env file

- set proper connection to the database
- add configuration for dingo/api package. See [detailed docs here](https://github.com/dingo/api/wiki/Configuration)
- required configuration string is `API_DOMAIN=yourdomain.dev`

### Register middleware

Add middleware to App/Http/Kernel.php to the $routeMiddleware array.

```php
'check.role.access' => CheckAccess::class,
```

### Swagger configuration

Add '/routes' path in 'config/l5-swagger.php', annotation path.
```php
'annotations' => [base_path('app'), base_path('routes')],
```

# Generating code for REST API project

* Run artisan command for code scaffolding.

```php
php artisan make:rest-api-project
```

* Generate swagger documentation.

```php
php artisan l5-swagger:generate
```

* Execute command

```php
composer dump-autoload
```

* :elephant: :boom: :+1: :+1:
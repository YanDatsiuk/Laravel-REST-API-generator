<?php

namespace TMPHP\RestApiGenerators\Helpers;

/**
 * Class RestAuth
 *
 * @package TMPHP\RestApiGenerators\Helpers
 */
class RestAuth
{
    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public static function routes()
    {
        $api = app('Dingo\Api\Routing\Router');

        // Authentication Routes...
        $api->group([
            'middleware' => 'check.role.access',
            'version' => env('API_VERSION', 'v1'),
            'prefix' => env('API_VERSION', 'v1'),
        ], function ($api) {
            //Login Routes
            $api->post('/login', 'App\REST\Http\Controllers\Api\v1\AuthController@login')
                ->name('login');
            $api->post('/logout', 'App\REST\Http\Controllers\Api\v1\AuthController@logout')
                ->name('logout');

            // Registration Routes...
            $api->post('/register', 'App\REST\Http\Controllers\Api\v1\AuthController@register')
                ->name('register');

            // Reset password Routes
            $api->post('/password/email',
                'App\REST\Http\Controllers\Api\v1\ForgotPasswordController@sendResetLinkEmail')
                ->name('password.email');
            $api->post('/password/reset',
                'App\REST\Http\Controllers\Api\v1\ResetPasswordController@reset')
                ->name('password.reset');
        });
    }
}
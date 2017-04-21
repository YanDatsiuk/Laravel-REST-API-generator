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
        $api->group(['middleware' => 'check.role.access', 'version' => env('APP_ENV', 'v1')], function ($api) {
            //Login Routes
            $api->post('/login', 'App\REST\Http\Controllers\Api\v1\Auth\AuthController@login')->name('login');
            $api->post('/logout', 'App\REST\Http\Controllers\Api\v1\Auth\AuthController@logout')->name('logout');

            // Registration Routes...
            $api->post('/register', 'App\REST\Http\Controllers\Api\v1\Auth\AuthController@register')->name('register');

            // Reset password Routes
            $api->post('/password/email',
                'App\REST\Http\Controllers\Api\v1\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
            $api->post('/password/reset',
                'App\REST\Http\Controllers\Api\v1\Auth\ResetPasswordController@reset')->name('password.reset');
        });
    }
}
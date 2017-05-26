<?php

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
*/

$api->group([
    'version' => env('API_VERSION', 'v1'),
    'prefix' => env('API_VERSION', 'v1'),
], function ($api) {

    /**
     * @SWG\Post(
     *     path="/login",
     *     tags={"authentication"},
     *     description="Login Request",
     *     produces= {"application/json"},
     *
     *     @SWG\Parameter(
     *         name="loginParam`s",
     *         in="body",
     *         description="Email,Password",
     *         required=true,
     *         @SWG\Schema(
     *             ref="#/definitions/login"
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         description="Set authorization token type(JWT) in header and return AuthUser"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Access Forbidden"
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/error"
     *         )
     *     )
     * )
     */
    $api->post('/login', 'App\REST\Http\Controllers\Api\v1\AuthController@login')
        ->name('login');

    /**
     * @SWG\Post(
     *     path="/logout",
     *     tags={"authentication"},
     *     description="Logout",
     *     produces= {"application/json"},
     *
     *     @SWG\Parameter(
     *         name="x-app-authorization",
     *         in="header",
     *         description="Bearer token",
     *         required=true,
     *         type="string"
     *     ),
     *
     *     @SWG\Response(
     *         response="204",
     *         description="NoContent"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Token has been blacklisted"
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/error"
     *         )
     *     )
     * )
     */
    $api->post('/logout', 'App\REST\Http\Controllers\Api\v1\AuthController@logout')
        ->name('logout');

    /**
     * @SWG\Post(
     *     path="/register",
     *     tags={"authentication"},
     *     description="Register request",
     *     produces= {"application/json"},
     *
     *     @SWG\Parameter(
     *         name="",
     *         in="body",
     *         description="",
     *         required=true,
     *         @SWG\Schema(
     *             ref="#/definitions/register"
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         description="Set authorization token type(JWT) in header and return AuthUser"
     *     ),
     *
     *     @SWG\Response(
     *         response="403",
     *         description="Access forbidden"
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/error"
     *         )
     *     )
     * )
     */
    $api->post('/register', 'App\REST\Http\Controllers\Api\v1\AuthController@register')
        ->name('register');

    /**
     * @SWG\Post(
     *     path="/password/email",
     *     tags={"authentication"},
     *     description="Request a password reset link into email",
     *     produces= {"application/json"},
     *
     *     @SWG\Parameter(
     *         name="",
     *         in="body",
     *         description="",
     *         required=true,
     *         @SWG\Schema(
     *             ref="#/definitions/reset-link-request"
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         description=""
     *     ),
     *
     *     @SWG\Response(
     *         response="403",
     *         description="Access forbidden"
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/error"
     *         )
     *     )
     * )
     */
    $api->post('/password/email',
        'App\REST\Http\Controllers\Api\v1\ForgotPasswordController@sendResetLinkEmail')
        ->name('password.email');

    /**
     * @SWG\Post(
     *     path="/password/reset",
     *     tags={"authentication"},
     *     description="Reset password using token from email",
     *     produces= {"application/json"},
     *
     *     @SWG\Parameter(
     *         name="",
     *         in="body",
     *         description="",
     *         required=true,
     *         @SWG\Schema(
     *             ref="#/definitions/reset"
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         description=""
     *     ),
     *
     *     @SWG\Response(
     *         response="403",
     *         description="Access forbidden"
     *     ),
     *     @SWG\Response(
     *         response="default",
     *         description="unexpected error",
     *         @SWG\Schema(
     *             ref="#/definitions/error"
     *         )
     *     )
     * )
     */
    $api->post('/password/reset',
        'App\REST\Http\Controllers\Api\v1\ResetPasswordController@reset')
        ->name('password.reset');
});
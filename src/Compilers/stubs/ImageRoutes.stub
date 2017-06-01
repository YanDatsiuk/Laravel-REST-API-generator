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

        //images CRUD
        $api->group(
            ['prefix' => '/images'],
            function ($api) {

                /**
                 * @SWG\Get(
                 *     path="/images",
                 *     tags={"images"},
                 *     description="Get list of images",
                 *     produces= {"application/json"},
                 *
                 *      @SWG\Parameter(
                 *         name="Authorization",
                 *         in="header",
                 *         description="JWTAuthToken example - Bearer {token}",
                 *         required=true,
                 *         type="string"
                 *     ),
                 *      @SWG\Parameter(
                 *         name="include[]",
                 *         in="query",
                 *         description="List of model relations with limit and offset parameter. Example: {relationName}:limit({limit}|{offset})",
                 *         default="",
                 *         required=false,
                 *         type="array",
                 *         @SWG\Items(
                 *             type="string"
                 *         ),
                 *         collectionFormat="multi"
                 *     ),
                 *     @SWG\Parameter(
                 *         name="page",
                 *         in="query",
                 *         description="Page number",
                 *         required=false,
                 *         default=1,
                 *         type="integer"
                 *     ),
                 *     @SWG\Parameter(
                 *         name="limit",
                 *         in="query",
                 *         description="Items limit per page",
                 *         required=false,
                 *         default=10,
                 *         type="integer"
                 *     ),
                 *     @SWG\Response(
                 *         response="200",
                 *         description="Requested resource collection"
                 *     ),
                 *
                 *     @SWG\Response(
                 *         response="403",
                 *         description="Access forbidden"
                 *     ),
                 *     @SWG\Response(
                 *         response="default",
                 *         description="Unexpected error",
                 *         @SWG\Schema(
                 *             ref="#/definitions/error"
                 *         )
                 *     )
                 * )
                 */
                $api->get('/', 'App\REST\Http\Controllers\Api\v1\ImageController@index')
                    ->name('images');

                /**
                 * @SWG\Get(
                 *     path="/images/show/{id}",
                 *     tags={"images"},
                 *     description="Get specific image by Id",
                 *     produces= {"application/json"},
                 *
                 *      @SWG\Parameter(
                 *         name="Authorization",
                 *         in="header",
                 *         description="JWTAuthToken example - Bearer {token}",
                 *         required=true,
                 *         type="string"
                 *     ),
                 *
                 *     @SWG\Parameter(
                 *         name="id",
                 *         in="path",
                 *         required=true,
                 *         type="integer"
                 *     ),
                 *
                 *     @SWG\Response(
                 *         response="200",
                 *         description="image with specified id"
                 *     ),
                 *
                 *     @SWG\Response(
                 *         response="403",
                 *         description="Access forbidden"
                 *     ),
                 *     @SWG\Response(
                 *         response="422",
                 *         description="Unprocessable entity"
                 *     ),
                 *     @SWG\Response(
                 *         response="default",
                 *         description="Unexpected error",
                 *         @SWG\Schema(
                 *             ref="#/definitions/error"
                 *         )
                 *     )
                 * )
                 */
                $api->get('/show/{id}', 'App\REST\Http\Controllers\Api\v1\ImageController@show')
                    ->name('images.show.id');

                /**
                 * @SWG\Post(
                 *     path="/images/upload",
                 *     tags={"images"},
                 *     description="Upload image",
                 *     produces= {"application/json"},
                 *
                 *     @SWG\Parameter(
                 *         name="x-app-authorization",
                 *         in="header",
                 *         description="JWTAuthToken example - Bearer token",
                 *         required=true,
                 *         type="string"
                 *     ),
                 *     @SWG\Parameter(
                 *         name="image",
                 *         in="formData",
                 *         description="file",
                 *         required=true,
                 *         type="file",
                 *         default=""
                 *     ),
                 *
                 *     @SWG\Response(
                 *         response="200",
                 *         description="Created resource"
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
                 *             ref="#/definitions/Error"
                 *         )
                 *     )
                 * )
                 */
                $api->post('/upload/', 'App\REST\Http\Controllers\Api\v1\ImageController@upload')
                    ->name('images.upload');

                /**
                 * @SWG\Patch(
                 *     path="/images/update/{id}",
                 *     tags={"images"},
                 *     description="Update specific image by Id",
                 *     produces= {"application/json"},
                 *
                 *     @SWG\Parameter(
                 *         name="Authorization",
                 *         in="header",
                 *         description="JWTAuthToken example - Bearer {token}",
                 *         required=true,
                 *         type="string"
                 *     ),
                 *     @SWG\Parameter(
                 *         name="id",
                 *         in="path",
                 *         description="",
                 *         required=true,
                 *         type="integer"
                 *     ),
                 *     @SWG\Parameter(
                 *         name="",
                 *         in="body",
                 *         description="",
                 *         required=true,
                 *         @SWG\Schema(
                 *             ref="#/definitions/image"
                 *         )
                 *     ),
                 *
                 *     @SWG\Response(
                 *         response="200",
                 *         description="Updated entity"
                 *     ),
                 *
                 *     @SWG\Response(
                 *         response="403",
                 *         description="Access forbidden"
                 *     ),
                 *     @SWG\Response(
                 *         response="422",
                 *         description="Unprocessable entity"
                 *     ),
                 *     @SWG\Response(
                 *         response="default",
                 *         description="Unexpected error",
                 *         @SWG\Schema(
                 *             ref="#/definitions/error"
                 *         )
                 *     )
                 * )
                 */
                $api->patch('/update/{id}', 'App\REST\Http\Controllers\Api\v1\ImageController@update')
                    ->name('images.update');

                /**
                 * @SWG\Delete(
                 *     path="/images/delete/{id}",
                 *     tags={"images"},
                 *     description="Delete specific image by Id",
                 *     produces= {"application/json"},
                 *
                 *     @SWG\Parameter(
                 *         name="Authorization",
                 *         in="header",
                 *         description="JWTAuthToken example - Bearer {token}",
                 *         required=true,
                 *         type="string"
                 *     ),
                 *     @SWG\Parameter(
                 *         name="id",
                 *         in="path",
                 *         description="",
                 *         required=true,
                 *         type="integer"
                 *     ),
                 *
                 *     @SWG\Response(
                 *         response="204",
                 *         description="Deleted entity"
                 *     ),
                 *
                 *     @SWG\Response(
                 *         response="403",
                 *         description="Access forbidden"
                 *     ),
                 *     @SWG\Response(
                 *         response="422",
                 *         description="Unprocessable entity"
                 *     ),
                 *     @SWG\Response(
                 *         response="default",
                 *         description="Unexpected error",
                 *         @SWG\Schema(
                 *             ref="#/definitions/error"
                 *         )
                 *     )
                 * )
                 */
                $api->delete('/delete/{id}', 'App\REST\Http\Controllers\Api\v1\ImageController@destroy')
                    ->name('images.delete');
            }
        );
});
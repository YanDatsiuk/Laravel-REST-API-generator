<?php

namespace TMPHP\RestApiGenerators\Helpers\Traits\Auth;

use Dingo\Api\Http\Request;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\Helpers\Traits\ErrorFormatable;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Trait Authenticatable
 * @package TMPHP\RestApiGenerators\Helpers\Traits
 */
trait Authenticatable
{
    use ErrorFormatable, Helpers;

    protected $userModel;

    /**
     * User registration
     *
     * @param Request $request
     * @return $this
     */
    public function register(Request $request)
    {
        //validation //todo catch ValidationException here to avoid 500 status code
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|between:6,255'
        ]);

        //create user instance
        $this->userModel
            ->fill(
                [
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'password' => bcrypt($request->input('password')),
                ]
            )
            ->save();

        //get token
        $token = 'Bearer ' . JWTAuth::fromUser($this->userModel);

        //return token in response header
        return $this->response->noContent()->header(
            'authorization',
            $token
        );
    }

    /**
     * Logging in user
     *
     * @param Request $request
     * @return $this|\Dingo\Api\Http\Response
     */
    public function login(Request $request)
    {

        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $token = JWTAuth::attempt($request->only(['email', 'password']));

        if ($token) {
            //return token in response header
            return $this->response->noContent()->header(
                'authorization',
                'Bearer ' . $token
            );
        } else {
            return $this->response->errorForbidden('Access Forbidden');
        }
    }

    /**
     * Logging out user
     *
     * @param Request $request
     */
    public function logout(Request $request)
    {
        //todo delete token

        //todo return response
    }
}
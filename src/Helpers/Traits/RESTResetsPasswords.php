<?php

namespace TMPHP\RestApiGenerators\Helpers\Traits;

use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * trait RESTResetsPasswords
 *
 * @package TMPHP\RestApiGenerators\Helpers\Traits
 */
trait RESTResetsPasswords
{
    use ErrorFormatable, Helpers;

    private static $token;

    /**
     * Reset the given user's password.
     *
     * @param Request $request
     *
     * @return \Dingo\Api\Http\Response
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
            $this->resetPassword($user, $password);
        }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse()
            : $this->sendResetFailedResponse($response);
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Get the password reset credentials from the request.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Contracts\Auth\CanResetPassword $user
     * @param  string $password
     *
     * @return void
     */
    protected function resetPassword($user, $password)
    {
        $user->forceFill(['password' => bcrypt($password),])->save();

        static::$token = $this->authManager()->attempt(['email' => $user->email, 'password' => $password]);
    }

    /**
     * Get the guard to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function authManager()
    {
        return JWTAuth::getFacadeRoot();
    }

    /**
     * Get the response for a successful password reset
     *
     * @return \Dingo\Api\Http\Response
     */
    protected function sendResetResponse()
    {
        return $this->response->accepted()->withHeader('Authorization', static::$token);
    }

    /**
     * Get the response for a failed password reset.
     *
     * @param string $response
     *
     * @return Response
     */
    protected function sendResetFailedResponse($response)
    {
        return $this->responseErrorMessage(trans($response), 422, ['email' => trans($response)]);
    }
}
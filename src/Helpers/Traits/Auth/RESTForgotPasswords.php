<?php

namespace TMPHP\RestApiGenerators\Helpers\Traits\Auth;

use Dingo\Api\Http\Request;
use Dingo\Api\Http\Response;
use Dingo\Api\Routing\Helpers;
use Illuminate\Support\Facades\Password;
use TMPHP\RestApiGenerators\Helpers\Traits\ErrorFormatable;

/**
 * trait RESTForgotPasswords
 *
 * @package TMPHP\RestApiGenerators\Helpers\Traits
 */
trait RESTForgotPasswords
{
    use ErrorFormatable, Helpers;

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        $this->validate($request, ['email' => 'required|email']);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse()
            : $this->sendResetLinkFailedResponse($response);
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
     * Get the response for a successful password reset link.
     *
     * @return Response
     */
    protected function sendResetLinkResponse()
    {
        return $this->response->accepted();
    }

    /**
     * Get the response for a failed password reset link.
     *
     * @param  string $response
     *
     * @return Response
     */
    protected function sendResetLinkFailedResponse($response)
    {
        return $this->responseErrorMessage(trans($response), 422, ['email' => trans($response)]);
    }
}
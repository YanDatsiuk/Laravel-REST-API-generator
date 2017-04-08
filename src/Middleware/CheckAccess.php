<?php

namespace TMPHP\RestApiGenerators\Middleware;

use TMPHP\RestApiGenerators\Helpers\ErrorFormatable;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class CheckAccess
 * @package App\Http\Middleware
 */
class CheckAccess
{
    use ErrorFormatable;

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        //disabling
        if (env('APP_ENV') === 'local'){
            return $next($request);
        }

        $query = Role::with('actions')->whereName('subscriber');
        $roles = $query->get();

        try {
            $token = Helper::customParseJWTToken();

            if ($token) {
                $user = \Tymon\JWTAuth\Facades\JWTAuth::toUser($token);
                if ($user) {
                    $roles = $user->roles;
                }
            };
        } catch (JWTException $e) {
        }

        //Checking access in user/guest actions to the current endpoint
        if ($this->checkAccessToAction($roles)) {
            return $next($request);
        } else {
            return $this->responseErrorMessage(
                'Access forbidden',
                403,
                [Action::class => 'You don\'t have permission to access']
            );
        }
    }

    /**
     * AccessChecker
     *
     * @param $roles
     * @return bool Check access in actions to current route name
     */
    private function checkAccessToAction($roles)
    {
        foreach ($roles as $role) {
            foreach ($role->actions as $action) {
                if ($action->name === Route::currentRouteName()) {
                    return true;
                }
            }
        }

        return false;
    }
}
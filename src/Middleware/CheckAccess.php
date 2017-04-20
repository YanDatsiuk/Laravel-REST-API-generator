<?php

namespace TMPHP\RestApiGenerators\Middleware;

use Dingo\Api\Facade\Route;
use TMPHP\RestApiGenerators\Helpers\Traits\ErrorFormatable;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        /* Disable check access*/
        if ( ! env('CHECK_ACCESS')) {
            return $next($request);
        }

        $roles = $this->role()->with('actions')->whereName('subscriber')->get();

        if (JWTAuth::getToken()) {
            if ($user = JWTAuth::toUser()) {
                $roles = $user->roles;
            }
        }

        //Checking access in user/guest actions to the current endpoint
        if ($this->checkAccessToAction($roles)) {
            return $next($request);
        }

        return $this->responseErrorMessage('Access forbidden', 403, ['You don\'t have permission to access']);
    }

    /**
     * Define class of Roles
     *
     * @return \App\REST\Role
     */
    public function role()
    {
        return new \App\REST\Role();
    }

    /**
     * Access checker
     *
     * @param $roles
     *
     * @return bool Check access in actions to current route name
     */
    private function checkAccessToAction($roles)
    {
        $actions = array_unique($roles->pluck('actions')->flatten()->pluck('name')->toArray());

        return in_array($this->route()->currentRouteName(), $actions, true);
    }

    /**
     * Define class of router
     *
     * @return mixed
     */
    public function route()
    {
        return Route::getFacadeRoot();
    }

    /**
     * Define class of actions
     *
     * @return \App\REST\Action
     */
    public function action()
    {
        return new \App\REST\Action();
    }
}
<?php

namespace TMPHP\RestApiGenerators\Middleware;

use Dingo\Api\Facade\Route;
use Illuminate\Support\Facades\Log;
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
        if (!env('CHECK_ACCESS')) {
            return $next($request);
        }

        if ($user = JWTAuth::toUser(JWTAuth::getToken())) {

        }

        //Checking access in user/guest actions to the current endpoint
        if ($this->checkAccessToAction($user)) {
            return $next($request);
        }

        return $this->responseErrorMessage('Access forbidden', 403, ['You don\'t have permission to access']);
    }

    /**
     * Check whether a current user have access to endpoint (action)
     * todo refactor: if we generate relation User->actions... we don't have to do all these cycles.
     * @param $user
     * @return bool
     */
    private function checkAccessToAction($user)
    {
        $authGroupUsers = $user->authGroupUsers;

        $groups = collect([]);
        foreach ($authGroupUsers as $authGroupUser){
            $groups->push($authGroupUser->group);
        }

        $authActionGroups = collect([]);
        foreach ($groups as $group){
            $authActionGroups->push($group->authActionGroups);
        }
        $authActionGroups = $authActionGroups->flatten();

        $actions = collect([]);
        foreach ($authActionGroups as $authActionGroup){
            $actions->push($authActionGroup->action);
        }

        $actionNames = array_unique($actions->pluck('name')->toArray());

        return in_array($this->route()->currentRouteName(), $actionNames, true);
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
     * Define class of Roles
     *
     * @return \App\REST\Role
     */
    public function role()
    {
        return new \App\REST\Role();
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
<?php

namespace TMPHP\RestApiGenerators\Middleware;

use Dingo\Api\Facade\Route;
use Illuminate\Support\Facades\Log;
use TMPHP\RestApiGenerators\Helpers\Traits\ErrorFormatable;
use Tymon\JWTAuth\Exceptions\JWTException;
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

        try {
            $user = JWTAuth::toUser(JWTAuth::getToken());
        } catch (JWTException $exception) {
            $user = null;
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
        if ($user) {
            $aclGroupUsers = $user->aclGroupUsers;

            $groups = collect([]);
            foreach ($aclGroupUsers as $aclGroupUser) {
                $groups->push($aclGroupUser->group);
            }
        } else {
            $groups = collect([]);
            $modelsNamespace = config('rest-api-generator.namespaces.models');
            $aclGroupModel = $modelsNamespace.'\AclGroup';
            $guestGroup = $aclGroupModel::firstOrCreate(['name' => 'guest']);
            $groups->push($guestGroup);
        }

        $aclActionGroups = collect([]);
        foreach ($groups as $group) {
            $aclActionGroups->push($group->aclActionGroups);
        }
        $aclActionGroups = $aclActionGroups->flatten();

        $actions = collect([]);
        foreach ($aclActionGroups as $aclActionGroup) {
            $actions->push($aclActionGroup->action);
        }

        $actionNames = array_unique($actions->pluck('name')->toArray());

        return in_array($this->route()->currentRouteName(), $actionNames, true);

        //todo refactor - proper flow:
        //check whether user provided
        //else use "guest" group actions
        //get user's actions (unique between all user's groups)
        //return true or false
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
     * Define class of Groupa
     *
     * @return \App\REST\Group
     */
    public function group()
    {
        return new \App\REST\Group();
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
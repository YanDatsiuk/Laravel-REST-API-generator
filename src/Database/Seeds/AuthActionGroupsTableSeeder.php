<?php

namespace TMPHP\RestApiGenerators\Database\Seeds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AuthActionGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelsNamespace = config('rest-api-generator.namespaces.models');
        $authActionModel = $modelsNamespace.'\AuthAction';
        $authGroupModel = $modelsNamespace.'\AuthGroup';
        $authActionGroupModel = $modelsNamespace.'\AuthActionGroup';

        //get all actions
        $allActions = $authActionModel::all();

        //get all read actions
        $readActions = $this->onlyReadActions($allActions);

        //assign all actions to "admin" group
        $adminGroup = $authGroupModel::firstOrCreate(['name' => 'admin']);
        $this->assignActionsToGroup($allActions, $adminGroup);

        //assign read actions to "guest" and "registered" group
        $guestGroup = $authGroupModel::firstOrCreate(['name' => 'guest']);
        $this->assignActionsToGroup($readActions, $guestGroup);

        $registeredGroup = $authGroupModel::firstOrCreate(['name' => 'registered']);
        $this->assignActionsToGroup($readActions, $registeredGroup);
    }

    private function onlyReadActions(Collection $allActions){

        //todo filter
        return $allActions;
    }

    private function assignActionsToGroup(Collection $actions, Model $group){

        foreach ($actions as $action){
            DB::table('auth_action_group')->insert([
                'action_id' => $action->id,
                'group_id' => $group->id,
            ]);
        }
    }
}

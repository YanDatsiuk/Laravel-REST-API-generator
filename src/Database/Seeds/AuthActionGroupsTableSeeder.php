<?php

namespace TMPHP\RestApiGenerators\Database\Seeds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AuthActionGroupsTableSeeder extends Seeder
{

    private $authActionGroupModel;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modelsNamespace = config('rest-api-generator.namespaces.models');
        $authActionModel = $modelsNamespace . '\AuthAction';
        $authGroupModel = $modelsNamespace . '\AuthGroup';
        $this->authActionGroupModel = $modelsNamespace . '\AuthActionGroup';

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

    /**
     * Filter actions to get only show and index endpoints.
     *
     * @param Collection $actions
     * @return Collection
     */
    private function onlyReadActions(Collection $actions)
    {
        //filter all action to get only show and index endpoints
        $readActions = collect([]);
        foreach ($actions as $action) {
            if (str_contains($action->name, ['create', 'update', 'delete'])) {
                continue;
            } else {
                $readActions->push($action);
            }
        }

        return $readActions;
    }

    /**
     * Assign actions to group.
     *
     * @param Collection $actions
     * @param Model $group
     */
    private function assignActionsToGroup(Collection $actions, Model $group)
    {
        foreach ($actions as $action) {
            $this->authActionGroupModel::firstOrCreate([
                'action_id' => $action->id,
                'group_id' => $group->id,
            ]);
        }
    }
}

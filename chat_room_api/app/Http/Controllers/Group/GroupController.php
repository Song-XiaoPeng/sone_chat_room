<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Model\Group;
use App\Http\Model\UserGroupRelation;
use App\User;
use Illuminate\Http\Request;

/**
 * Class GroupController
 *
 * @package \\${NAMESPACE}
 */
class GroupController extends Controller
{
    public function create()
    {

    }

    public function joinGroup(Request $request)
    {
        $group_id = $request->id;
        $uid = User::Auth()->id;
        $data = [
            'uid' => $uid,
            'group_id' => $group_id
        ];
        UserGroupRelation::firstOrCreate($data);
        return self::jsonReturn();
    }

    public function lists()
    {
        $res = Group::with('tags')->get();
        $hasJoined = UserGroupRelation::where('uid', Auth::user()->id)->pluck('group_id');
        foreach ($res as $v) {
            if (in_array($v['id'], $hasJoined)) {
                $v['status'] = 1;
            } else {
                $v['status'] = 0;
            }
        }
        return self::jsonReturn($res);
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 18-5-27
 * Time: 下午6:48
 */

namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{
    public function getUserInfo()
    {
        $res = User::with('info')->find(1)->toArray();
        return $res;
        dd($res);
    }
}
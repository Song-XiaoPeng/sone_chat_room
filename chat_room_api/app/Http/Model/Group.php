<?php
/**
 * Created by PhpStorm.
 * User: sone
 * Date: 18-5-27
 * Time: 下午6:45
 */

namespace App\Http\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

//群聊表
class Group extends Model
{
    protected $table = 'g_group';

    public function tags()
    {
        return $this->belongsToMany(GroupTag::class, 't_tag_group_relationship', 'group_id', 'tag_id');
    }

    public function groupMsg()
    {
        return $this->hasMany(GroupMsg::class, 'id', 'group_id');
    }

    //群聊所有成员 多对多
    public function members()
    {
        return $this->belongsToMany(User::class, 'u_user_group_relationship', 'group_id', 'uid');
    }

}
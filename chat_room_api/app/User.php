<?php

namespace App;

use App\Http\Model\Attachment;
use App\Http\Model\Group;
use App\Http\Model\SingleChatMsg;
use App\Http\Model\UserFriendsRelation;
use App\Http\Model\UserGroupRelation;
use App\Http\Model\UserInfo;
use App\Http\Model\UserTag;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    protected $table = 'u_user';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'is_forbid', 'last_ip'
    ];

    //不会被批量赋值的属性
    protected $guarded = ['created_time', 'updated_time'];

    //用户基础信息
    public function info()
    {
        return $this->hasOne(UserInfo::class, 'uid', 'id');
    }

    //用户上传的文件
    public function attachment()
    {
        return $this->hasMany(Attachment::class, 'uid', 'id');
    }

    //用户标签
    public function tag()
    {
        return $this->hasMany(UserTag::class, 'uid', 'id');
    }

    //聊天记录 1对多
    public function chatMsg()
    {
        return $this->hasMany(SingleChatMsg::class, 'send_uid', 'id');
    }

    //好友 多对多
    public function friends()
    {
        return $this->belongsToMany(self::class, 'u_user_friends_relationship', 'uid', 'friend_uid');
    }

    //加入的群聊
    public function joinedGroups()
    {
        return $this->belongsToMany(Group::class, 'u_user_group_relationship', 'uid', 'group_id');
    }
}

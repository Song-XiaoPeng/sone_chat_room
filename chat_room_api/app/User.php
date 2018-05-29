<?php

namespace App;

use App\Http\Model\UserInfo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;
    protected $table = 'u_user';

    /**
     * The attributes that are mass assignable.
     * 可以被批量赋值的属性
     * @var array
     */
//    protected $fillable = [
//        'username', 'email', 'password', 'phone', ''
//    ];

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

    public function info()
    {
        return $this->hasOne(UserInfo::class, 'uid', 'id');
    }
}

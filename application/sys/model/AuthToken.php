<?php

namespace app\sys\model;

use app\lib\Safe;
use think\Request;

class AuthToken extends BaseModel
{
    protected $table = "sys_user_token";
    protected $hidden = ['update_time'];


    public function user()
    {
        return $this->hasOne('AuthUser', 'user_id', 'user_id');
    }


    //根据id获取用户token
    public static function usertoken($id)
    {
        $usertoken = self::get($id);
        return $usertoken;
    }

    //根据token来获取用户id
    public static function getIdByToken($token = '')
    {
        if (empty($token)) {
            $token = Request::instance()->header('token');
        }
        $id = self::get(['token' => $token])->user_id;
        return $id;
    }

    //根据用户id在中间表生成token
    public static function createToken($id)
    {
        $tokenData = Safe::generateToken();
        self::create([
            'user_id' => $id,
            'token' => $tokenData['token'],
            'expire_time' => date("Y-m-d H:i:s",$tokenData['expire']),
            'update_time' => date("Y-m-d H:i:s",time())
        ]);
    }

    //登录时更新用户Token
    public static function updateToken($id)
    {
        $tokenData = Safe::generateToken();
        self::update([
            'user_id' => $id,
            'token' => $tokenData['token'],
            'expire_time' => date("Y-m-d H:i:s",$tokenData['expire']),
            'update_time' => date("Y-m-d H:i:s",time())
        ]);
    }
}

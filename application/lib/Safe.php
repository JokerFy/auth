<?php

namespace app\lib;

class Safe
{

    // 密码加密
    public static function setPassword($pwd,$salt)
    {
        $pwd = md5(md5($pwd).$salt);
        return $pwd;
    }

    // 生成令牌
    public static function generateToken()
    {
        $str = md5(uniqid(md5(microtime(true)), true)); //uniqid第二个参数加true会带上一个额外的内容避免多机部署token重复
        $randChar = getRandChar(32);
        $token = sha1($str . $randChar);
        $data = [
            'token'=>$token,
            'expire'=>time()+config('extra.token_expire')
        ];
        return $data;
    }
}

<?php

namespace app\sys\validate;

use app\common\exception\ParameterException;
use app\common\validate\BaseValidate;
use app\lib\Safe;

use app\sys\model\auth\User;
use think\captcha\Captcha;

class LoginValidate extends BaseValidate
{
    protected $rule = [
        'username' => 'require|min:4|accountValidation',
        'password' => 'require|min:6',
//        'captcha'  =>  'require|captchaValidation'
    ];

    //验证码验证
    protected function captchaValidation($value,$rule,$data)
    {
        $res = $this->check_verify($value,$data['uuid']);
        if(!$res)
            throw new ParameterException([
                'msg'=>'验证码错误'
            ]);
        return true;
    }

    //账号验证
    protected function accountValidation($value,$rule,$data)
    {
        $adminSalt = User::get(['username' =>$value]);
        if($adminSalt){
            $password = Safe::setpassword($data['password'], $adminSalt->salt);
            $admin = User::get([
                'username' => $value,
                'password' => $password
            ]);
        }
        if (!$adminSalt || !$admin) {
            throw new ParameterException([
                'msg' => '账号或密码错误'
            ]);
        }
        return true;
    }

    protected  function check_verify($code, $id = '')
    {
        $captcha = new Captcha();
        return $captcha->check($code, $id);
    }
}

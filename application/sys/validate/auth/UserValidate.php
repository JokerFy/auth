<?php
/**
 * Created by PhpStorm.
 * User: fin
 * Date: 2019/11/1
 * Time: 20:25
 */

namespace app\sys\validate\auth;

use app\common\exception\ParameterException;
use app\common\validate\BaseValidate;
use app\sys\model\auth\User;

class UserValidate extends BaseValidate
{
    protected $rule = [
        'username'  =>  'require',
        'email' =>  'email',
        'password' =>  'require',
        'mobile' =>  'require|isMobile',
//        'nickname' =>  'require|max:25',
    ];

    protected $message = [
        'username.require'  =>  '用户名必须',
        'email' =>  '邮箱格式错误',
        'password' =>  '密码格式错误',
        'mobile' =>  '手机号格式错误',
//        'nickname' =>  '昵称格式错误',
    ];

    public function checkPost($data){
        $this->getDataByRule($data);
    }

    //账号验证
    public function userIdValidation($value)
    {
        $rule = ['id'=>'require|number'];
        $data = ['id'=>$value];
        $this->checkFieldByRule($data,$rule);
        $user = User::get(['user_id' =>$value]);
        if (!$user) {
            throw new ParameterException([
                'msg' => '账号信息不存在'
            ]);
        }
        return true;
    }

}


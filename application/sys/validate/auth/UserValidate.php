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
        'id' => 'require|number'
    ];

    //账号验证
    public function UserIdValidation($value)
    {
        $this->goCheck($value);
        $user = User::get(['user_id' =>$value]);
        if (!$user) {
            throw new ParameterException([
                'msg' => '账号信息不存在'
            ]);
        }
        return true;
    }

    public function addUserValidate(){

    }
}


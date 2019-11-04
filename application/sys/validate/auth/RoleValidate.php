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
use app\sys\model\auth\Role;

class RoleValidate extends BaseValidate
{
    public function checkPost($data){
        $this->getDataByRule($data);
    }

    public function roleIdValidation($value)
    {
        $rule = ['role_id'=>'require|number'];
        $data = ['role_id'=>$value];
        $this->checkFieldByRule($data,$rule);
        $user = Role::get(['role_id' =>$value]);
        if (!$user) {
            throw new ParameterException([
                'msg' => '角色信息不存在'
            ]);
        }
        return true;
    }

}


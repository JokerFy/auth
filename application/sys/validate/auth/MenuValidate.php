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
use app\sys\model\auth\Menu;
use app\sys\model\auth\User;

class MenuValidate extends BaseValidate
{
    public function checkPost($data){
        $this->getDataByRule($data);
    }

    public function menuIdValidation($value)
    {
        $rule = ['menu_id'=>'require|number'];
        $data = ['menu_id'=>$value];
        $this->checkFieldByRule($data,$rule);
        $user = Menu::get(['menu_id' =>$value]);
        if (!$user) {
            throw new ParameterException([
                'msg' => '菜单信息不存在'
            ]);
        }
        return true;
    }

}


<?php
/**
 * Created by PhpStorm.
 * User: finley
 * Date: 2018/11/7
 * Time: 上午11:28
 */

namespace app\sys\validate;
use app\common\exception\TokenException;
use app\common\validate\BaseValidate;
use app\sys\model\Token;

class SysTokenValidate extends BaseValidate
{
    protected $rule = [
        'token'=>'require|tokenValidation'
    ];

    //判断token是否是有效的
    protected function tokenValidation($value)
    {
        $res = (new Token())->get(['token'=>$value]);
        if(!$res)
            throw new TokenException();
        return true;
    }
}

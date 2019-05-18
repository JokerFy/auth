<?php

namespace app\common\validate;

use  app\common\validate\BaseValidate;

class LoginValidate extends BaseValidate
{
    protected $rule = [
        'username' => 'require|min:5',
        'password' => 'require|min:6',
//        'captcha' => 'require'
    ];
}
<?php
/**
 * Created by PhpStorm.
 * User=>finley
 * Date=>2018/11/7
 * Time=>上午10:29
 */

namespace app\sys\controller\auth;
use think\captcha\Captcha;
use think\Controller;

class Test2 extends Controller
{
    public function index($uuid)
    {
        $captcha = new Captcha();
        return $captcha->entry($uuid);
        $res = request()->param('token');
        if(!$res){
            return 'error';
        }
        return $res;
    }
}
<?php

namespace app\sys\controller;

use think\Controller;
use app\sys\model\auth\{
    User, Token
};
use app\sys\validate\LoginValidate;
use app\lib\Safe;
use think\Db;
use think\Request;

class Common extends Controller
{
    /**
     * 后台管理员用户在后台添加，
     * 无需注册
     */
    public function login()
    {
        (new LoginValidate)->goCheck();
        $data = Request::instance()->param();
        $adminSalt = User::get(['username' => $data['username']]);
        $admin = User::get([
            'username' => input('username'),
            'password' => Safe::setpassword($data['password'], $adminSalt->salt)
        ]);
        //每次登录更新用户token
        Token::updateToken($admin->user_id);
        //获取用户token并返回
        return SuccessNotify(Token::usertoken($admin->user_id));
    }

    /**
     * @return mixed
     */
    public function register()
    {
        Db::startTrans();
        //生成管理员
        $user = User::createUser(input('username'), input('password'));
        if ($user) {
            //生成管理员token到关联表
            $token = Token::createToken($user->id);
            if ($token){
                //创建管理员和token都成功则提交
                Db::commit();
                return SuccessNotify($token);
            }
        }
        Db::rollback();
        throw ParameterException([
            'msg' => '注册失败'
        ]);
    }

    public function logout()
    {
        return SuccessNotify();

    }

    /**
     * 将菜单进行无限极递归分类
     * @param $menuData
     * @param int $parent_id
     * @return array
     */
    public function treeData($menuData,$parent_id=0){
        $treeData = [];
        foreach ($menuData as $key => $val){
            if($val['parent_id'] == $parent_id){
                //通过type将路由菜单的显示定在二级为止
                if($val['type'] == 0) {
                    $val['list'] = $this->treeData($menuData, $val['menu_id']);
                }
                $treeData[] = $val;
            }
        }
        return $treeData;
    }
}

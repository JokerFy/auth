<?php
/**
 * Created by PhpStorm.
 * User: finley
 * Date: 2018/11/8
 * Time: 23:26
 */

namespace app\sys\model;

use app\common\exception\AuthAccessException;

class Auth
{
    //检查访问者是否具有访问的权限
    public function check($access,$uid=''){
        $noCheck = ['sys:Auth.Menu:nav','sys:Auth.User:info'];
        if(in_array($access,$noCheck)){
            return true;
        }
        throw (new AuthAccessException([
            'msg'=>$access
        ]));
        $uid = empty($uid)??(new AuthToken())::getIdByToken();
        //获取用户
        $user = (new AuthUser())->get($uid);
        //获取用户所有的角色
        $userRoles = $user->roles;
        //获取用户所有的访问控制器方法的权限
        $userAccess = [];
        //$userRoles是一个二维数组，进行嵌套循环所有每个角色数组下的权限
        foreach ($userRoles as $key => $value) {
            foreach ($value->permissions->hidden(['pivot']) as $item => $val) {
                if ($val['perms'] != false) {
                    $userAccess[] = $val['perms'];
                }
            };
        }
        $userAccess = array_unique($userAccess);
        //对权限进行格式化
        foreach ($userAccess as $val) {
            $array = explode(',', $val);
            foreach ($array as $item) {
                $permissions[] = $item;
            }
        }
        //去除重复权限并且重新对索引进行排序
        $permissions = array_values(array_unique($permissions));

        if (!in_array($access,$permissions)){
            throw (new AuthAccessException([
                'msg'=>'您没有权限访问'
            ]));
        }

        return true;
    }
}
<?php

namespace app\lib;
use app\lib\RegisterTree;
use app\sys\model\auth\{Role,Token,User,Menu};
/**
 * 模型工厂
 * 因为很多地方都会要调用模型
 * 如果有变动要修改的话会很麻烦，因此使用模型工厂统一生产
 * Class ModelFactory
 * @package app\lib
 */
class ModelFactory
{
    //因为tp5的模型实例化已经自带了单例模式，所以不必我们自己进行单例化
    public static function AuthRole(){
        $model = new Role();
        RegisterTree::set('RoleModel',$model);
        return $model;
    }

    public static function AuthToken(){
        $model = new Token();
        RegisterTree::set('TokenModel',$model);
        return $model;
    }

    public static function AuthUser(){
        $model = new User();
        RegisterTree::set('UserModel',$model);
        return $model;
    }

    public static function AuthPermission(){
        $model = new Menu();
        RegisterTree::set('MenuModel',$model);
        return $model;
    }
}

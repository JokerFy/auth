<?php

namespace app\sys\controller\auth;

use app\sys\controller\Common;
use app\sys\controller\BaseController;
use app\sys\service\auth\MenuService;
use app\sys\validate\auth\MenuValidate;
use think\Request;

/**
 * 初始数据格式：
 * var dataList = [
    {
    'menuId': 1,
    'parentId': 0,
    'parentName': null,
    'name': '系统管理',
    'url': null,
    'perms': null,
    'type': 0,
    'icon': 'system',
    'orderNum': 0,
    'open': null,
    'list': null
    }...
 *菜单化格式：
 * var navDataList = [
    {
    'menuId': 1,
    'parentId': 0,
    'parentName': null,
    'name': '系统管理',
    'url': null,
    'perms': null,
    'type': 0,
    'icon': 'system',
    'orderNum': 0,
    'open': null,
    'list': [
    {
    'menuId': 2,
    'parentId': 1,
    'parentName': null,
    'name': '管理员列表',
    'url': 'sys/user',
    'perms': null,
    'type': 1,
    'icon': 'admin',
    'orderNum': 1,
    'open': null,
    'list': null
    },...
 * Class AuthMenu
 * @package app\sys\controller\auth
 */
class Menu extends BaseController
{
    public $menuSevice;
    public $menuValidate;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->menuSevice = new MenuService();
        $this->menuValidate = new MenuValidate();
    }
    /**
     * 菜单列表格式如下
         var dataList = [
        {
        'menuId': 1,
        'parentId': 0,
        'parentName': null,
        'name': '系统管理',
        'url': null,
        'perms': null,
        'type': 0,
        'icon': 'system',
        'orderNum': 0,
        'open': null,
        'list': null
        },...
     * @return \think\response\Json
     */
    public function list()
    {
        $data['menu'] = $this->menuSevice->list();
        return SuccessNotify($data);
    }

    //获得菜单信息
    public function info($id)
    {
        $this->menuValidate->menuIdValidation($id);
        $data['menu'] = $this->menuSevice->info($id);
        return SuccessNotify($data);
    }

    //获取上级菜单
    public function select()
    {
        $data['menuList'] = $this->menuSevice->select();
        return SuccessNotify($data);
    }

    //添加菜单
    public function save()
    {
        $data = $this->request->param();
        $this->menuSevice->save($data);
        return SuccessNotify();
    }

    //修改菜单
    public function update()
    {
        $data = $this->request->param();
        $this->menuSevice->update($data);
        return SuccessNotify();
    }

    //删除菜单（如果有子菜单则一起删除，包括中间表和菜单表）
    public function delete($id)
    {
        $this->menuValidate->menuIdValidation($id);
        $this->menuSevice->delete($id);
        return SuccessNotify();
    }

    /**
     * 根据用户的权限生成菜单树（因为前后端分离，所以这里相当于是获取了给前端显示菜单的路由）
     * menulist是用户的路由菜单权限，前端根据该数值动态显示菜单
     * permissions是用户的访问权限，前端根据该值判断用户能访问后端的哪些路由，并且根据权限判断是否显示增加删除等按钮
     *  data: {
        'msg': 'success',
        'code': 0,
        'menuList': navDataList(上面可以查看格式)
        'permissions': [
        'sys:schedule:info',
        'sys:menu:update',
        'sys:menu:delete',
        'sys:config:info',
        'sys:menu:list',
        'sys:config:save'......
         * @return \think\response\Json
         */
    public function nav()
    {
        $data = $this->menuSevice->nav($this->user_id);
        return SuccessNotify($data);
    }
}

<?php

namespace app\sys\controller\auth;

use app\sys\controller\Common;
use app\sys\controller\BaseController;
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
        $data = $this->AuthMenuModel::all();
        //给菜单增加一个上级菜单的属性
        foreach ($data as $key => $val) {
            foreach ($data as $item) {
                if ($val['parent_id'] == $item['menu_id']) {
                    $data[$key]['parent_name'] = $item['name'];
                } elseif ($val['parent_id'] == 0) {
                    $data[$key]['parent_name'] = '';
                }
            }
        }
        return SuccessNoMsg($data);
    }

    //获得菜单信息
    public function info($id)
    {
        $data['menu'] = $this->AuthMenuModel::get($id);
        return SuccessNotify($data);
    }

    //获取上级菜单
    public function select()
    {
        $data['menuList'] = $this->AuthMenuModel::all();
        //给菜单增加一个上级菜单的属性
        foreach ($data['menuList'] as $key => $val) {
            foreach ($data['menuList'] as $item) {
                if ($val['parent_id'] == $item['menu_id']) {
                    $data['menuList'][$key]['parent_name'] = $item['name'];
                } elseif ($val['parent_id'] == 0) {
                    $data['menuList'][$key]['parent_name'] = '';
                }
            }
        }
        return SuccessNotify($data);
    }

    //添加菜单
    public function save()
    {
        $data = $this->request->post();
        $this->AuthMenuModel->createMenu($data);
        return SuccessNotify();
    }

    //修改菜单
    public function update()
    {
        $data = $this->request->post();
        $this->AuthMenuModel->updateMenu($data);
        return SuccessNotify();
    }

    //删除菜单（如果有子菜单则一起删除，包括中间表和菜单表）
    public function delete($id)
    {
        $menu = $this->AuthMenuModel::get(['menu_id'=>$id]);
        $childMenu = $this->AuthMenuModel::all(['parent_id'=>$id]);
        //获取菜单在中间表匹配的角色
        $menusRole = $menu->roles;
        //删除中间表和菜单表数据
        $menu->deleteMenu($menusRole);
        $menu->delete();
        foreach ($childMenu as $val){
            $childMenuRole = $val->roles;
            $val->deleteMenu($childMenuRole);
            $val->delete();
        }
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
        $token = request::instance()->header('token');
        $userid = $this->AuthTokenModel::getIdByToken($token);
        $userPermisson = (new User())->userPermission($userid);
        //对菜单进行二级递归排序
        $menuList = (new Common())->treeData($userPermisson['userPermission']);
        $data = ['menuList' => $menuList, 'permissions' => $userPermisson['userAccess']];
        return SuccessNotify($data);
    }
}

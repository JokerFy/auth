<?php
/**
 * Created by PhpStorm.
 * User: fin
 * Date: 2019/11/1
 * Time: 19:58
 */

namespace app\sys\service\auth;

use app\sys\controller\Common;
use app\sys\model\auth\Menu;
use app\sys\model\auth\Token;
use app\sys\model\auth\User;

class MenuService
{
    public $tokenModel;
    public $userModel;

    public function __construct()
    {
        $this->tokenModel = new Token();
        $this->userModel = new User();
        $this->menuModel = new Menu();
        $this->userService = new UserService();
    }


    public function list(){
        $data = $this->menuModel::all();
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
        return $data;
    }

    public function info($id){
        return $this->menuModel::get($id);
    }

    public function select()
    {
        $data = $this->menuModel::all();
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
        return $data;
    }

    public function delete($id){
        $menu = $this->menuModel::get(['menu_id'=>$id]);
        $childMenu = $this->menuModel::all(['parent_id'=>$id]);
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
    }


    public function nav($userid)
    {
        $userPermisson = $this->userService->userPermission($userid);
        //对菜单进行二级递归排序
        $menuList = (new Common())->treeData($userPermisson['userPermission']);
        $data = ['menuList' => $menuList, 'permissions' => $userPermisson['userAccess']];
        return $data;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: fin
 * Date: 2019/11/1
 * Time: 19:58
 */

namespace app\sys\service\auth;
use app\sys\model\auth\Menu;
use app\sys\model\auth\Role;
use app\sys\model\auth\Token;
use app\sys\model\auth\User;
class RoleService
{
    public $tokenModel;
    public $userModel;
    public $menuModel;
    public $roleModel;
    public $userService;

    public function __construct()
    {
        $this->tokenModel = new Token();
        $this->userModel = new User();
        $this->menuModel = new Menu();
        $this->roleModel = new Role();
        $this->userService = new UserService();
    }

    /**
     * User: fin
     * Date: 2019/11/4
     * Time: 17:03
     * @param $id
     * @return mixed
     */
    public function info($id){
        $data['role'] = $this->roleModel::get($id);
        $menuList = $data['role']->permissions;
        $menuIdList = array();
        foreach ($menuList as $val) {
            $menuIdList[] = $val['menu_id'];
        }

        $data['role']['menuIdList'] = $menuList;
        unset($data['permissions']);
        return $data;
    }

    /**
     * User: fin
     * Date: 2019/11/4
     * Time: 17:03
     * @param $data
     * @return bool
     * @throws \think\exception\DbException
     */
    public function save($data)
    {
        $roleid = $this->roleModel->createRoles($data);
        //查找到当前角色
        $role = $this->roleModel->get($roleid);
        //保存权限
        $role->grantPermission($data['menuIdList']);
        return true;
    }

    /**
     * User: fin
     * Date: 2019/11/4
     * Time: 17:04
     * @param $roleData
     * @return bool
     * @throws \think\exception\DbException
     */
    public function update($roleData)
    {
        //更新角色
        $this->roleModel->updateRoles($roleData);
        $role = $this->roleModel->get($roleData['roleId']);
        //获取目前更新的角色的所有菜单
        $roleMenu = $role->permissions;
        //因为上传来的角色列表格式与我们数据库取得不一样，需要转换一下
        foreach ($roleMenu->toArray() as $menu) {
            $roleMenus[] = $menu['menu_id'];
        }

        //将上传来的角色列表和我们转换后的角色列表转换成集合，然后利用集合的差集算出需要增加和删除的权限有哪些
        $roleMenus = collection($roleMenus);
        $updateMenu = collection($roleData['menuIdList']);
        $addMenu = $updateMenu->diff($roleMenus);
        $deleteMenu = $roleMenus->diff($updateMenu);
        //批量增加菜单权限
        $role->grantPermission($addMenu->toArray());
        //批量删除菜单权限
        $role->deletePermission($deleteMenu->toArray());

        return true;
    }

    /**
     * 根据用户获取角色列表
     * User: fin
     * Date: 2019/11/4
     * Time: 16:49
     * @param $userid
     * @return \think\response\Json
     * @throws \think\exception\DbException
     */
    public function select($userid)
    {
        $data['list'] = $this->userModel->get($userid)->roles->toArray();
        //如果是该用户创建的角色，该用户具备分配权
        $childrenRole = $this->roleModel::all(['create_user_id' => $userid])->toArray();
        $data['list'] = array_merge($data['list'], $childrenRole);
        return $data;
    }

    /**
     * User: fin
     * Date: 2019/11/4
     * Time: 17:05
     * @param $ids
     * @throws \think\exception\DbException
     */
    public function delete($ids){
        $roles = $this->roleModel::all($ids);
        foreach ($roles as $key => $val) {
            //获取当前角色的所有权限
            $rolePermission = $val->permissions;
            //删除角色中间表中的权限
            $val->deletePermission($rolePermission);
            //删除角色
            $val->delete();
        }
    }

}

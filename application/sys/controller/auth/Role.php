<?php

namespace app\sys\controller\auth;

use think\Request;
use app\sys\controller\BaseController;

/**
 * Class AuthRole
 * @package app\sys\controller\auth
 * 角色信息格式：
 * data: {
 * 'msg': 'success',
 * 'code': 0,
 * 'role':{
 * 'roleId': '@increment',
 * 'roleName': '@name',
 * 'remark': '@csentence',
 * 'createUserId': 1,
 * 'menuIdList': '(1, 2, 3，4...)',
 * 'createTime': '@datetime'
 * }
 * }
 *角色列表格式：
 * data: {
 * 'msg': 'success',
 * 'code': 0,
 * 'page': {
 * 'totalCount': dataList.length,
 * 'pageSize': 10,
 * 'totalPage': 1,
 * 'currPage': 1,
 * 'list': dataList
 * }
 * }
 */
class Role extends BaseController
{
    /**
     * 获取角色列表
     * @auth: finley
     * @date: 2018/11/7 下午3:44
     * @param int $page //当前请求页面
     * @param int $limit //每页显示数量
     * @return \think\response\Json
     */
    public function list($page = 1, $limit = 10)
    {
        $data = $this->pageList($this->AuthRoleModel, $page, $limit);
        return SuccessNotify($data);
    }

    //根据用户获取角色列表
    public function select()
    {
        //根据请求头中的token去获取用户Id
        $userid = $this->AuthTokenModel::getIdByToken();
        $data['list'] = $this->AuthUserModel->get($userid)->roles->toArray();
        //如果是该用户创建的角色，该用户具备分配权
        $childrenRole = $this->AuthRoleModel::all(['create_user_id' => $userid])->toArray();
        $data['list'] = array_merge($data['list'], $childrenRole);
        return SuccessNotify($data);
    }

    public function judgeHalfCheck($menuIdList)
    {
        $idx = '';
        for($i=0;$i<count($menuIdList);$i++){
            for($j=$i+1;$j<count($menuIdList);$j++){
                if($menuIdList[$j]>$menuIdList[$i]){
                    $idx = 0;
                }else{
                    $idx = $menuIdList[$j];
                    return $j;
                }
            }
        }
    }

    //根据角色id获取角色信息
    public function info($id)
    {
        $data['role'] = $this->AuthRoleModel::get($id);
        $menuList = $data['role']->permissions;
        $menuIdList = array();
        foreach ($menuList as $val) {
            $menuIdList[] = $val['menu_id'];
        }

        $data['role']['menuIdList'] = $menuList;
        unset($data['permissions']);

        return SuccessNotify($data);
    }

    //创建角色
    public function save()
    {
        $roleData = $this->request->post();
        $roleid = $this->AuthRoleModel->createRoles($roleData);
        //查找到当前角色
        $role = $this->AuthRoleModel->get($roleid);
        //保存权限
        $role->grantPermission($roleData['menuIdList']);

        return SuccessNotify();
    }

    //修改角色
    public function update()
    {
        $roleData = $this->request->post();
        //更新角色
        $this->AuthRoleModel->updateRoles($roleData);
        $role = $this->AuthRoleModel->get($roleData['roleId']);
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

        return SuccessNotify();
    }

    //删除角色
    public function delete()
    {
        $roleIds = $this->request->post();
        $roles = $this->AuthRoleModel::all($roleIds);
        foreach ($roles as $key => $val) {
            //获取当前角色的所有权限
            $rolePermission = $val->permissions;
            //删除角色中间表中的权限
            $val->deletePermission($rolePermission);
            //删除角色
            $val->delete();
        }
        return SuccessNotify();
    }

    //检查角色有哪些权限
    public function rolePermission()
    {
        $role = $this->AuthRoleModel->get(8);
        return $role->permissions;
    }


}

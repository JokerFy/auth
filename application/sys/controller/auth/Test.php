<?php

namespace app\sys\controller\auth;

use app\api\model\User;
use think\Controller;
use app\auth\model\AdminPermission;
use app\auth\model\AdminRole;
use app\auth\model\AdminUser;
use think\Db;

class Test extends Controller
{
    public $roleModel;
    public $userModel;
    public $user;
    public $role;

    public function __construct()
    {
        //角色模型
        $this->roleModel = (new AdminRole);

        //用户模型
        $this->userModel = (new AdminUser);

        //当前用户
        $this->user = $this->userModel->get(4);

        //当前用户所拥有的角色
        $this->role = $this->user->roles;
    }

    public function index()
    {
        $a = collection([4,5,6]);
        $b = collection([1,2,3]);
        $res = $a->diff($b);
        print_r(phpinfo());
//        return json($res);
    }

    /**
     * 角色功能测试模块
     *
     * 角色篇：
     * 逻辑1：必须先创建角色并授权（可以无权限）
     * 逻辑2：删除角色（关联删除role_menu和role_user）
     * 逻辑3：检查角色有哪些权限
     * 逻辑4：修改角色权限（关联role_menu）
     *
     * 用户篇：
     * 逻辑1：创建用户，至少必须选择一个角色
     * 逻辑2：查看用户有哪些角色，查出所有的角色权限
     * 逻辑3：删除用户（关联删除role_user）
     * 逻辑4：修改用户的角色（关联role_user）
     *
     * 菜单权限篇（目录，菜单，按钮）：
     * 逻辑1：创建菜单权限
     * 逻辑2：查看用户是否拥有该权限
     * 逻辑3：删除权限（关联删除role_menu）
     *
     */

    /**
     * 将用户菜单权限生成菜单树
     * @param $menuData
     * @param int $parent_id
     * @return array
     */
    public function treeData($menuData,$parent_id=0){
        $treeData = [];
        foreach ($menuData as $key => $val){
            if($val['parent_id'] == $parent_id){
                $val['list'] = $this->treeData($menuData,$val['id']);
                $treeData[] = $val;
            }
        }
        return $treeData;
    }

    /**
     * 角色篇
     */
    //1.创建一个新角色并授权
    public function addRole()
    {
        $roleData = [
            'role_name' => '创建一个新角色功能测试',
            'remark' => '测试添加新角色功能',
            'create_user_id' => '1',
            'create_time' => '2018-10-31 15:36:45'
        ];

        $permissionList = [
            '0' => '6',
            '1' => '7',
            '2' => '8',
            '3' => '9',
            '4' => '10',
            '5' => '11',
            '6' => '12',
            '7' => '13',
            '8' => '14',
            '9' => '15'
        ];

        Db::startTrans();
        $roleid = $this->roleModel->create($roleData);

        //查找到当前角色
        $role = $this->roleModel->get($roleid->id);
        //获取当前角色的所有权限
        $rolePermission = $role->permissions;
        if($rolePermission){
        //删除现有的权限
        $role->deletePermission($rolePermission);
        }
        //更新新的权限
        $grantRes = $role->grantPermission($permissionList);

        if ($roleid && $grantRes) {
            Db::commit();
            return json($role->permissions);
        }
        Db::rollback();
        return json('失败');
    }

    //2.删除角色
    public function deleteRole(){
        $role = $this->roleModel->get(9);
        //获取当前角色的所有权限
        $rolePermission = $role->permissions;
        Db::startTrans();
        //删除角色中间表中的权限
        $permissonDel = $role->deletePermission($rolePermission);
        //删除角色
        $roleDel = $role->delete();
        if($permissonDel && $roleDel){
            Db::commit();
        }else{
            Db::rollback();
        }
    }

    //3.检查角色有哪些权限
    public function rolePermission(){
        $role = $this->roleModel->get(8);
        return $role->permissions;
    }

    //4.修改角色权限
    public function editRole(){
        $roleData = [
            'role_name' => 'hehe',
            'remark' => '测试添加新角色功能'
        ];

        $permissionList = [
            '0' => '6',
            '1' => '7',
            '2' => '8',
            '3' => '9',
            '4' => '10',
            '5' => '11',
            '6' => '12'
        ];

        Db::startTrans();
        $role = $this->roleModel->get(8);
        $updateRole = $role->save($roleData,['id'=>8]);
        //获取当前角色的所有权限
        $rolePermission = $role->permissions;
        if($rolePermission){
            //删除现有的权限
            $role->deletePermission($rolePermission);
        }
        //更新新的权限
        $grantRes = $role->grantPermission($permissionList);

        if ($updateRole && $grantRes) {
            Db::commit();
            return json($role->permissions);
        }
        Db::rollback();
        return json('失败');
    }


    /**
     * 用户篇
     */
    //1.增加用户
    public function addUser()
    {
        Db::startTrans();
        $addUser = $this->userModel->create([
            'username' => 'test5',
            'password' => '123456'
        ]);
        $user = $this->userModel->get($addUser->id);
        $res=$user->assignRole(['8']);

        if($res && $addUser){
            Db::commit();
            return json($user);
        }
        Db::rollback();
        return json('失败');
    }

    //2.获取用户所有角色的所有权限
    public function userPermission($id){
        //获取用户
        $user = $this->userModel->get($id);
        //获取用户所有的角色
        $userRoles = $user->roles;
        //获取用户所有的菜单路由权限
        $userPermission = [];
        //获取用户所有的访问控制器方法的权限
        $userAccess = [];

        //$userRoles是一个二维数组，进行嵌套循环所有每个角色数组下的权限
        foreach($userRoles as $key => $value){
            foreach ($value->permissions->hidden(['pivot']) as $item => $val){
                $userPermission[] = $val;
                if($val['perms']!=false){
                    $userAccess[] = $val['perms'];
                }
            };
        }

        $userPermission = array_unique($userPermission);
        $userAccess = array_unique($userAccess);

        $data = [
            'userPermission'=>$userPermission,
            'userAccess'=>$userAccess
        ];
        return $data;
    }

    //3.删除用户
    public function deleteUser(){
        $user = $this->userModel->get(9);
        //获取所有角色
        $roles = $user->roles;
        //删除中间表下所有角色
        $user->deleteRole($roles);
        //删除用户
        $user->delete();
    }

    /**
     * 4.根据用户的权限生成菜单树（因为前后端分离，所以这里相当于是获取了给前端显示菜单的路由）
     * menulist是用户的路由菜单权限，前端根据该数值动态显示菜单
     * permissions是用户的访问权限，前端根据该值判断用户能访问后端的哪些路由，并且根据权限判断是否显示增加删除等按钮
     * @return \think\response\Json
     */
    public function userMenuData(){
        $userPermisson = $this->userPermission(2);
        $menuList = $this->treeData($userPermisson['userPermission']);
        $permissions = $userPermisson['userAccess'];
        $data = ['data'=>['menulist'=>$menuList,'permissions'=>$permissions]];
        return json($data);
    }
    
}
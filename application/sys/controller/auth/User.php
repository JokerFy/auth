<?php

namespace app\sys\controller\auth;

use app\sys\controller\BaseController;
use app\sys\model\Auth;
use app\sys\service\auth\UserService;
use app\sys\validate\auth\UserValidate;
use think\Request;

class User extends BaseController
{
    public $userSevice;
    public $userValidate;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->userSevice = new UserService();
        $this->userValidate = new UserValidate();
    }

    /**
     * 获取管理员列表
     * @auth: finley
     * @date: 2018/11/7 下午3:44
     * @param int $page //当前请求页面
     * @param int $limit //每页显示数量
     * @return \think\response\Json
     */
    public function list($page = 1, $limit = 10)
    {
        $data = $this->pageList($this->AuthUserModel, $page, $limit);
        return SuccessNotify($data);
    }

    /**
     * 根据参数获取用户信息
     * 用户信息格式：
     * data: {
     * 'msg': 'success',
     * 'code': 0,
     * 'user':{
     * 'userId': '@increment',
     * 'username': '@name',
     * 'email': '@email',
     * 'mobile': /^1[0-9]{10}$/,
     * 'status': 1,
     * 'roleIdList': (1,2,3,4),
     * 'createUserId': 1,
     * 'createTime': 'datetime'
     * }}
     */
    public function info($id = 0)
    {
        //如果不存在指定id则获取当前登录用户信息
        if(!$id){
            $id = $this->user_id;
        }
        $this->userValidate->UserIdValidation($id);
        $data = $this->userSevice->getInfo($id);
        return SuccessNotify($data);
    }

    //删除用户(可批量)
    public function delete()
    {
        $ids = $this->request->post();
        $this->userSevice->delete($ids);
        return SuccessNotify();
    }

    //增加用户
    public function save()
    {
        $data = $this->request->post();
        $this->userSevice->save($data);
        return SuccessNotify();
    }

    //修改用户
    public function update()
    {
        $data = $this->request->post();
        //更新用户
        $this->AuthUserModel->updateUser($data);
        //手动获取用户，因为save方法返回的不是数据集
        $user = $this->AuthUserModel->get($data['userId']);
        //获取目前更新的用户的所有角色
        $userRole = $user->roles;
        //因为上传来的角色列表格式与我们数据库取得不一样，需要转换一下
        if (!$userRole->isEmpty()) {
            foreach ($userRole->toArray() as $role) {
                $userRoles[] = $role['role_id'];
            }
            //将上传来的角色列表和我们转换后的角色列表转换成集合，然后利用集合的差集算出需要增加和删除的权限有哪些
            $userRoles = collection($userRoles);
            $updateRole = collection($data['roleIdList']);
/*            $addRoles = $updateRole->diff($userRoles);
            $deleteRoles = $userRoles->diff($updateRole);
            foreach ($addRoles as $role) {
                $user->assignRole($role);
            }*/
            foreach ($userRoles as $role) {
                $user->deleteRole($role);
            }
            foreach ($updateRole as $role) {
                $user->assignRole($role);
            }
        } else {
            $user->assignRole($data['roleIdList']);
        }
        return SuccessNotify();
    }

    //修改当前用户密码
    public function password()
    {
        return SuccessNotify();
    }

    //获取用户所有角色的所有权限
    public function userPermission($id)
    {
        //获取用户
        $user = $this->AuthUserModel->get($id);
        //获取用户所有的角色
        $userRoles = $user->roles;
        //获取用户所有的菜单路由权限
        $userPermission = [];
        //获取用户所有的访问控制器方法的权限
        $userAccess = [];
        //$userRoles是一个二维数组，进行嵌套循环所有每个角色数组下的权限
        foreach ($userRoles as $key => $value) {
            foreach ($value->permissions->hidden(['pivot']) as $item => $val) {
                $userPermission[] = $val;
                if ($val['perms'] != false) {
                    $userAccess[] = $val['perms'];
                }
            };
        }
        $userPermission = array_unique($userPermission);
        $userAccess = array_unique($userAccess);

        //对权限进行格式化
        foreach ($userAccess as $val) {
            $array = explode(',', $val);
            foreach ($array as $item) {
                $perms[] = $item;
            }
        }
        //去除重复权限并且重新对索引进行排序
        $userAccess = array_values(array_unique($perms));

        $data = [
            'userPermission' => $userPermission,
            'userAccess' => $userAccess
        ];
        return $data;
    }

    public function test()
    {
        $request = Request::instance();
        $access = $request->module() . ':' . $request->controller() . ':' . $request->action();
        $userAccess = (new Auth())->check($access);
        return json($access);
    }

}

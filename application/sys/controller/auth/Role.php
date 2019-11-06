<?php

namespace app\sys\controller\auth;

use app\sys\service\auth\RoleService;
use app\sys\validate\auth\RoleValidate;
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
    public $roleSevice;
    public $roleValidate;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->roleSevice = new RoleService();
        $this->roleValidate = new RoleValidate();
    }

    public function list($page = 1, $limit = 10)
    {
        $listQuery = Request::instance()->param();
        $data = $this->pageList($this->AuthRoleModel,$listQuery, $page, $limit);
        return SuccessNotify($data);
    }

    //根据用户id获取角色列表
    public function select()
    {
        $data = $this->roleSevice->select($this->user_id);
        return SuccessNotify($data);
    }

    //根据角色id获取角色信息
    public function info($id)
    {
        $this->roleValidate->roleIdValidation($id);
        $data = $this->roleSevice->info($id);
        return SuccessNotify($data);
    }

    //创建角色
    public function save()
    {
        $roleData = $this->request->param();
        $this->roleSevice->save($roleData);
        return SuccessNotify();
    }

    //修改角色
    public function update()
    {
        $roleData = $this->request->param();
        $this->roleSevice->update($roleData);
        return SuccessNotify();
    }

    //可批量删除角色
    public function delete()
    {
        $roleIds = $this->request->post();
        $this->roleSevice->delete($roleIds);
        return SuccessNotify();
    }

    //检查角色有哪些权限
    public function rolePermission()
    {
        $role = $this->AuthRoleModel->get(8);
        return $role->permissions;
    }


}

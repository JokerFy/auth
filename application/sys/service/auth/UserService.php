<?php
/**
 * Created by PhpStorm.
 * User: fin
 * Date: 2019/11/1
 * Time: 19:58
 */

namespace app\sys\service\auth;


use app\sys\model\auth\Token;
use app\sys\model\auth\User;

class UserService
{
    public $tokenModel;
    public $userModel;

    public function __construct()
    {
        $this->tokenModel = new Token();
        $this->userModel = new User();
    }

    /**
     * 获取管理员信息
     * User: fin
     * Date: 2019/11/1
     * Time: 20:58
     * @param int $id
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo($id = 0){
        $data['user'] = $this->userModel::with('roles')->find($id);
        //roles是目前登录用户拥有的角色列表，roleIdList是获取的指定用户所拥有的角色列表
        foreach ($data['user']['roles']->toArray() as $val) {
            $roleIdList[] = $val['role_id'];
        }
        $data['user']['roleIdList'] = isset($roleIdList) ? $roleIdList : [];
        return $data;
    }


    /**
     * 删除管理员
     * User: fin
     * Date: 2019/11/1
     * Time: 20:58
     * @param $userIds
     * @throws \think\exception\DbException
     */
    public function delete($userIds){
        $users = $this->userModel::all($userIds);
        foreach ($users as $key => $val) {
            //获取每个用户角色，然后删除
            $roles[$key] = $val->roles;
            $val->deleteRole($roles[$key]);
            $val->delete();
            $this->tokenModel::destroy(['user_id'=>$val['user_id']]);
        }
    }

    /**
     * 新增管理员
     * User: fin
     * Date: 2019/11/1
     * Time: 20:58
     * @param $data
     * @throws \think\exception\DbException
     */
    public function save($data){
        $addUser = $this->userModel->createUser($data);
        $user = $this->userModel->get($addUser);
        //分配权限
        $user->assignRole($data['roleIdList']);
        //生成管理员token到关联表
        $this->tokenModel::createToken($user->user_id);
    }

}

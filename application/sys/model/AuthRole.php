<?php

namespace app\sys\model;

class AuthRole extends BaseModel
{

    protected $table = 'sys_role';
    protected $hidden = ['pivot', 'create_user_id'];
    protected $updateTime = false;
    protected $type = [
        'role_id' => 'integer',
        'status' => 'integer'
    ];

    public function users()
    {
        return $this->belongsToMany('AuthUser');
    }

    //当前角色的所有权限
    public function permissions()
    {
        return $this->belongsToMany('AuthMenu', 'sys_role_menu', 'menu_id', 'role_id');
    }

    //添加一个角色
    public function createRole($data)
    {
        return $this->allowField(true)->save($data);
    }

    //给角色赋予权限
    public function grantPermission($permission)
    {
        return $this->permissions()->saveAll($permission);
    }

    //取消角色赋予的权限
    public function deletePermission($permission)
    {
        return $this->permissions()->detach($permission);
    }

    //判断角色是否有权限
    public function hasPermission($permission)
    {
        //判断集合中是否有某个对象
        return $this->permissions->contains($permission);
    }

    //创建一个角色并保存
    public function createRoles($data){
        $res = self::create([
            'role_name' => $data['roleName'],
            'remark'=>$data['remark'],
            'create_user_id'=>(new AuthToken())->getIdByToken()
        ]);
        return $res->role_id;
    }

    //更新角色并保存
    public function updateRoles($data){
        $res = self::save([
            'role_name' => $data['roleName'],
            'remark'=>$data['remark'],
        ],['role_id'=>$data['roleId']]);
        return $res;
    }
}

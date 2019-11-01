<?php

namespace app\sys\model\auth;

use app\sys\model\BaseModel;

class Menu extends BaseModel
{
    protected $table = 'sys_menu';
    protected $type = [
        'type' => 'integer'
    ];

    //菜单拥有哪些角色
    public function roles()
    {
        return $this->belongsToMany('Role','sys_role_menu','role_id', 'menu_id');
    }

    //删除中间表中的menu权限
    public function deleteMenu($menu)
    {
        return $this->roles()->detach($menu);
    }

    public function createMenu($data){
        self::create([
            'parent_id'=>$data['parentId'],
            'name'=>$data['name'],
            'icon'=>$data['icon'],
            'url'=>$data['url'],
            'perms'=>$data['perms'],
            'type'=>$data['type'],
            'order_num'=>$data['orderNum']
        ]);
    }

    public function updateMenu($data){
        self::save([
            'parent_id'=>$data['parentId'],
            'name'=>$data['name'],
            'icon'=>$data['icon'],
            'url'=>$data['url'],
            'perms'=>$data['perms'],
            'type'=>$data['type'],
            'order_num'=>$data['orderNum']
        ],['menu_id'=>$data['menuId']]);
    }
}

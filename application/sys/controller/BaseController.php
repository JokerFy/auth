<?php

namespace app\sys\controller;

use app\common\exception\ParameterException;
use app\sys\controller\auth\User;
use app\sys\model\Auth;
use app\sys\validate\SysTokenValidate;
use app\lib\{RegisterTree,ModelFactory};
use think\Controller;
use think\Request;

class BaseController extends Controller
{
    protected $AuthMenuModel;
    protected $AuthTokenModel;
    protected $AuthRoleModel;
    protected $AuthUserModel;
    protected $token;
    protected $user_id;

    public function _initialize()
    {
        //判断是否有token并且token是否有效
        (new SysTokenValidate())->goCheck();

        $request = Request::instance();
        $access = $request->module() . ':' . $request->controller() . ':' . $request->action();
//        $userAccess = (new Auth())->check($access);
        //使用工厂模式对使用到的模型进行创建，然后子类继承后可以直接调用模型，减少复用
        $this->AuthMenuModel = ModelFactory::AuthPermission();
        $this->AuthTokenModel = ModelFactory::AuthToken();
        $this->AuthRoleModel = ModelFactory::AuthRole();
        $this->AuthUserModel = ModelFactory::AuthUser();
        $this->token = request::instance()->header('token');
        $this->user_id = $this->AuthTokenModel::getIdByToken($this->token);
    }


    /**
     * 根据请求模型进行数据分页
     * @auth: finley
     * @date: 2018/11/7 下午3:46
     * @param $model 请求模型
     * @param int $page 当前请求页
     * @param int $limit 每页显示数量
     * @return mixed
     */
    public function pageList($model,$condition,$page=1,$limit=10)
    {
        if($condition['page']){
            $page = $condition['page'];
            unset($condition['page']);
        }
        if($condition['limit']){
            $limit = $condition['limit'];
            unset($condition['limit']);
        }
        //取出所有数据
        $modelData = $model::all($condition);
        //分页处理
        $pageOffset = ($page-1)*$limit;
        $pageList = $model->where($condition)->limit($pageOffset,$limit)->select();
        $totalCount = count($modelData);
        //根据接口信息对数据进行分页
        $page = [
            //数据的总条数
            'totalCount' => $totalCount,
            'pageSize' => $limit,
            //总条数/每页的数量等于总页数
            'totalPage' => ceil($totalCount / $limit),
            'currPage' => $page,
            'list' => $pageList,
        ];
        $data['page'] = $page;
        return $data;
    }
}

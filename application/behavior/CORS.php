<?php
/**
 * Created by PhpStorm.
 * User: fin
 * Date: 2019/10/18
 * Time: 18:10
 */

namespace app\behavior;


class CORS
{
    public function appInit(&$params)
    {
        header('Access-Control-Allow-Origin: http://localhost:8001');
        header("Access-Control-Allow-Headers: token,Origin, X-Requested-With, Content-Type, Accept,x-token");
        header('Access-Control-Allow-Methods: POST,GET,DELETE,PUT');
        header('Access-Control-Allow-Credentials: true');
        if(request()->isOptions()){
            exit();
        }
    }
}

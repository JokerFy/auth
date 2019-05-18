<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

//生成随机字符串
function getRandChar($length)
{
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;
    for ($i = 0;
         $i < $length;
         $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}

// 接口返回形式
function SuccessNotify($data = [])
{
    $result = array(
        'code' => 0,
        'msg' => '成功',
    );
    $result = array_merge($result, parse_field($data));
    return json($result);
}

//接口返回形式（不带通知信息）
function SuccessNoMsg($data = [])
{
    return json(parse_field($data));
}

/**
 * 主要用于将数据库中有下划线的字段转换为驼峰式命名
 * 如role_id = roleId,create_user_id = createUserId
 * */
function parse_field($arr)
{
    $array = [];
    if (gettype($arr) == 'object') {
        $arr = $arr->toArray();
    }

    foreach ($arr as $key => $val) {
        //如果是数组代表是多重数组嵌套
        if (is_array($val)) {
            $array[$key] = parse_field($val);
        } elseif (gettype($val) == 'object') {
            //可能数据是对象
            $array[$key] = parse_field($val->toArray());
        } else {
            $newKey = preg_replace_callback('/_+([a-z])/', function ($matches) {
                return strtoupper($matches[1]);
            }, $key);
            $array[$newKey] = $val;
        }
    }
    return $array;
}

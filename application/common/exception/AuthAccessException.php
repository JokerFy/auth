<?php

namespace app\common\exception;

/**
 * token验证失败时抛出此异常 
 */
class AuthAccessException extends BaseException
{
    public $code = 200;
    public $msg = '没有访问权限';
    public $errorCode = 10002;
}
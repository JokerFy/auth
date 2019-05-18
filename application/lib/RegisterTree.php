<?php
namespace app\lib;

class RegisterTree
{
    protected static $objects;

    /**
     * 将对象映射到注册树上
     * @param $alias
     * @param $object
     */
    public static function set($alias,$object)
    {
        self::$objects[$alias] = $object;
    }

    public static function get($name)
    {
        return self::$objects[$name];
    }

    public static function _unset($alias)
    {
        unset(self::$objects[$alias]);
    }
}

<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/6/8 18:42
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\cache;


use think\Config;

class Redis
{
    private static $read_instance = null;
    private static $write_instance = null;

    public static function write()
    {
        $option = [
            'host' => Config::get('cache.redis_w_host') ?? '127.0.0.1',
            'port' => Config::get('cache.redis_w_port') ?? 6379,
        ];
        if (is_null(self::$write_instance)) {
            self::$write_instance = new \Redis();
            self::$write_instance->connect($option['host'], $option['port']);
        }
        return self::$write_instance;
    }

    public static function read()
    {
        $option = [
            'host' => Config::get('cache.redis_r_host') ?? '127.0.0.1',
            'port' => Config::get('cache.redis_r_port') ?? 6379,
        ];
        if (is_null(self::$read_instance)) {
            self::$read_instance = new \Redis();
            self::$read_instance->connect($option['host'], $option['port']);
        }
        return self::$read_instance;
    }

    //字符串类型------------------------------------------
    /**
     * 赋值 默认永久有效
     * @param $key
     * @param $value
     * @param int $expire
     * @return bool
     */
    public static function set($key, $value, $expire = 0)
    {
        if ($expire == 0) {
            return self::write()->set($key, $value);
        } else {
            return self::write()->setex($key, $expire, $value);
        }
    }

    /**
     * 查询缓存值，单个或数组
     * @param $key 键
     * @return mixed
     */
    public static function get($key)
    {
        $func = is_array($key) ? 'getMulti' : 'get';
        return self::read()->{$func}($key);
    }

    /**
     * 自增
     * @param $key
     * @param int $number
     * @return int
     */
    public static function incr($key, $number = 1)
    {
        if ($number == 1) {
            return self::write()->incr($key);
        } else {
            return self::write()->incrBy($key, $number);
        }
    }

    /**
     * 自减
     * @param $key
     * @param int $number
     * @return int
     */
    public static function decr($key, $number = 1)
    {
        if ($number == 1) {
            return self::write()->decr($key);
        } else {
            return self::write()->decrBy($key, $number);
        }
    }

    /**
     * 获取字符串长度
     * @param $key
     * @return int
     */
    public static function strlen($key)
    {
        return self::read()->strlen($key);
    }

    // Hash类型------------------------------------------

    /**
     * 赋单个值
     * @param $key
     * @param $field
     * @param $value
     * @return int
     */
    public static function hSet($key, $field, $value)
    {
        return self::write()->hSet($key, $field, $value);
    }

    /**
     * 取指定字段值，或取所有字段名和字段的值
     * @param $key
     * @param string $field
     * @return array|string
     */
    public static function hGet($key, $field = '')
    {
        if (empty($field)) {
            return self::read()->hGetAll($key);
        } else {
            return self::read()->hGet($key, $field);
        }
    }

    /**
     * 赋多个值
     * @param $key
     * @param $data ['field1'=>'value1','field2'=>'value2']
     * @return bool
     */
    public static function hMset($key, $data)
    {
        return self::write()->hMset($key, $data);
    }

    /**
     * 取多个值
     * @param $key
     * @param $dataField ['field1','field2']
     * @return array
     */
    public static function hMGet($key, $dataField)
    {
        return self::read()->hMGet($key, $dataField);
    }


    /**
     * 获取字段的数量
     * @param $key
     * @return int
     */
    public static function hLen($key)
    {
        return self::read()->hLen($key);
    }

    /**
     * 自增
     * @param $key
     * @param $field
     * @param int $number
     * @return int
     */
    public static function hIncrBy($key, $field, $number = 1)
    {
        return self::write()->hIncrBy($key, $field, $number);
    }


    // List类型------------------------------------------

    /**
     * 向列表左端添加元素
     * @param $key
     * @param $value
     * @return int
     */
    public static function lPush($key, $value)
    {
        return self::write()->lPush($key, $value);
    }

    /**
     * 向列表右端弹出元素
     * @param $key
     * @return string
     */
    public static function rPop($key)
    {
        return self::read()->rPop($key);
    }

    /**
     * 返回列表的长度
     * @param $key
     * @return int
     */
    public static function lLen($key)
    {
        return self::read()->lLen($key);
    }
}
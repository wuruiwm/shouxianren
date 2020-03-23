<?php
// +----------------------------------------------------------------------
// | tplay [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/3/22
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------
namespace app\lib\cache;



use think\cache\driver\Redis;

class BaseRedis extends Redis
{
    public static function conn(){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
    }
}
<?php
// +----------------------------------------------------------------------
// | api [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/3/21
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
    public $statusCode = 400;
    public $msg = '系统默认: 参数规则错误~';
    public $data= [];
    public $code = 10000;


    public function __construct($params=[])
    {
        if(!is_array($params)){
            return;
        }
        if(array_key_exists('statusCode',$params)){
            $this->statusCode = $params['statusCode'];
        }
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('msg',$params)){
            $this->msg = $params['msg'];
        }
        if(array_key_exists('data',$params)){
            $this->data = $params['data'];
        }
    }
}
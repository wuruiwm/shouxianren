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

use think\exception\Handle;
use Exception;
use think\facade\Config;
use think\facade\Log;
use think\facade\Request;



class ExceptionHandler extends Handle
{
    private $statusCode;
    private $msg;
    private $data;
    private $code;



    public function render(\Exception $e)
    {
        if($e instanceof BaseException){
            $this->statusCode = $e->statusCode;
            $this->msg = $e->msg;
            $this->data = $e->data;
            $this->code = $e->code;
            \think\Log::close();
        }else{

            if(\think\Config::get('app_debug')){
                \think\Log::close();
                return parent::render($e);
            }else{
                $this->statusCode = 500;
                $this->msg = '未知错误:服务器无法处理该请求';
                $this->data = $e->data;
                $this->code = 999;
                $this->recordErrorLog($e);
            }
        }

        $request = \think\Request::instance();

        $resultData = [
            'msg'       => $this->msg,
            'code'     => $this->code,
            'requestUrl'    => $request->url(true),
        ];

        if(!empty($this->data)){
            $newData['data'] = $this->data;
            $this->array_insert($resultData,2,$newData);
        }
        return json($resultData,$this->statusCode);
    }

    private function recordErrorLog(\Exception $e){
        \think\Log::record('[1]内部错误：'.$e->getMessage(),'error');
        \think\Log::record('[2]文件地址：'.$e->getFile(),'error');
        \think\Log::record('[3]错误行号：'.$e->getLine(),'error');
    }

    private function array_insert (&$array, $position, $insert_array) {
        $first_array = array_splice ($array, 0, $position);
        $array = array_merge ($first_array, $insert_array, $array);
    }


}
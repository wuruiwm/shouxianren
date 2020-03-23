<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2018/12/19
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;

use app\api\model\BalanceLog;
use app\api\model\IntegralLog;
use app\api\model\User;
use app\lib\exception\HttpErrorExpetion;
use think\Controller;

class BaseController extends Controller
{

    protected function mustBePost()
    {
        if (!request()->isPost()) throw new HttpErrorExpetion();
    }

    protected function mustBeGet()
    {
        if (!request()->isGet()) throw new HttpErrorExpetion();
    }


    /**
     * @param $user_id 用户ID
     * @param string $type 操作类型 2积分，3余额
     * @param string $number 数目
     * @param string $sign 符号，1增 2减
     * @param array $data // 可查看数据库字段说明进行操作
    $data = [
    'use_action'=>3,
    'type'=>2,
    'amount'=>$number,
    'user'=>$user_id,
    'remark'=>'兑换优惠券',
    ];
     */
    protected function integraOrBalanceLog($user_id,$data,$type,$number,$sign){
        if($type==2){
            if($sign==1){
                User::where('id',$user_id)->setInc('integral', $number);
            }else{
                User::where('id',$user_id)->setDec('integral', $number);
            }
            IntegralLog::createIntegralLog($data);
        }
        if($type==3){
            if($sign==1){
                User::where('id',$user_id)->setInc('balance', $number);
            }else{
                User::where('id',$user_id)->setDec('balance', $number);
            }
            BalanceLog::createBalanceLog($data);
        }
    }

}
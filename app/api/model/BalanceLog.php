<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/15
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


use think\Db;
use think\Exception;

class BalanceLog extends BaseModel
{
    public static function createBalanceLog($data)
    {
        $data['source_type'] = 4;
        return self::create($data);
    }

    public static function userRechargeBalance($post,$user_id)
    {
        if($post['type']==1){
            $type = '微信';
        }else{
            $type = '支付宝';
        }
        $data =[
            'use_action'=>2,
            'type'=>1,
            'order'=>$post['order'],
            'amount'=>$post['amount'],
            'user'=>$user_id,
            'source_type'=>$post['type'],
            'remark'=>'用户使用'.$type.'充值'
        ];

        Db::startTrans();
        try{
            $rechargeconfig = Rechargeconfig::get(1);

            if ($rechargeconfig['balance'] > 0) {
                $rebate = $data['amount'] * $rechargeconfig['balance'] / 100;
                $integralData = [
                    'use_action' => 3,
                    'type' => 1,
                    'amount' => $rebate,
                    'user' => $user_id,
                    'source_type' => 1,
                    'remark' => '用户使用'.$type.'充值，额外返积分'
                ];
                User::where('id', $user_id)->setInc('integral', $rebate);
                IntegralLog::create($integralData);
                self::create($data);
            }else{
                self::create($data);
            }

            User::where('id', $user_id)->setInc('balance', $data['amount']);
            Db::commit();

            return true;
        }catch (Exception $ex){
            Db::rollback();
            return false;
        }
    }
    public static function getListByUser($user_id)
    {
        return self::where('use_action','2')
            ->where('source_type','in','1,2')
            ->where('user',$user_id)
            ->order('create_time desc')
            ->select();
    }

    public static function userRechargeBalanceWxPayNotify($orderNo,$type)
    {

        $orderInfo = self::where('order', $orderNo)->find();
        if ($orderInfo['status'] == 0) {
            Db::startTrans();
            try {
                $rechargeconfig = Rechargeconfig::get(1);

                // 充值 额外返利 积分
                if ($rechargeconfig['integral'] > 0) {
                    $rebate = $orderInfo['amount'] * $rechargeconfig['integral'] / 100;
                    $integralData = [
                        'use_action' => 3,
                        'type' => 1,
                        'amount' => $rebate,
                        'user' => $orderInfo['user'],
                        'source_type' => $type,
                        'remark' => $orderInfo['remark'] . '，额外返积分'
                    ];
                    User::where('id', $orderInfo['user'])->setInc('integral', $rebate);
                    IntegralLog::create($integralData);
                }
                if ($rechargeconfig['balance'] > 0) {
                    $rebate = $orderInfo['amount'] * $rechargeconfig['balance'] / 100;
                    $balanceData = [
                        'use_action' => 2,
                        'type' => 1,
                        'amount' => $rebate,
                        'user' => $orderInfo['user'],
                        'source_type' => $type,
                        'remark' => $orderInfo['remark'] . '，额外返余额'
                    ];
                    User::where('id', $orderInfo['user'])->setInc('balance', $rebate);
                    BalanceLog::create($balanceData);
                }


                self::update(['status' => 1], ['order' => $orderInfo['order']]);
                User::where('id', $orderInfo['user'])->setInc('balance', $orderInfo['amount']);
                Db::commit();

                return true;
            } catch (Exception $ex) {
                Db::rollback();
                return false;
            }
        }

    }
}

<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/23
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


use app\admin\model\Attachment;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;

class User extends BaseModel
{

    public function getBirthdayAttr($value)
    {
        return timeToStr($value, 'Y-m-d');
    }

    public function getIntegralAttr($value)
    {
        return floatval($value);
    }


    public function getBalanceAttr($value)
    {
        return floatval($value);
    }

    public function getAvatarAttr($value)
    {
        $result = Attachment::where('id', $value)->field(['id', 'filepath'])->find();
        return [
            'id' => $result['id'],
            'filepath' => is_https() . $result['filepath']
        ];
    }

    public function setBirthdayAttr($value)
    {
        return GetMkTime($value);
    }


    public static function exist($key, $value, $except = [], $boolean = true)
    {
        return self::field($except, $boolean)->where([$key => $value])->find();
    }

    public static function logCheck($data, $except = [], $boolean = true)
    {
        return self::field($except, $boolean)->where(['mobile' => $data['mobile']])->where(['password' => $data['password']])->find();
    }

    public static function createUser($data)
    {
        return self::create($data);
    }

    public static function updateUser($id, $data)
    {
        $self = new User();
        return $self->allowField(true)->save($data, ['id' => $id]);
    }


    public static function userMemberCentre($user_id)
    {
        // 用户累计充值余额数量
        $rechargeBalanceTotal = BalanceLog::where([
            'use_action'=>2,
            'type'=>1,
            'user'=>$user_id,
        ])->where('source_type','in','1,2')
            ->sum('amount');
        // 用户信息
        $userInfo = self::get($user_id);

        // 判断用户今日是否签到
        $isCheckIn = self::isCheckIn($user_id);
        if(!$isCheckIn){
            $data['check_in']= [
                'status'=>'0',
                'msg'=>'签到',
                'number'=>'0'
            ];
        }else{
            $data['check_in'] = [
                'status'=>'1',
                'msg'=>'已签到',
                'number'=>'1'
            ];
        }

        // 判断用户今日是否已分享
        $isShare = self::isShare($user_id);
        if(!$isShare){
            $data['share'] = [
                'status'=>'0',
                'msg'=>'分享',
                'number'=>'0'
            ];
        }else{
            $data['share'] = [
                'status'=>'1',
                'msg'=>'已分享',
                'number'=>'1'
            ];
        }


        $data['user'] =[
            'balance_total'=>floatval($rechargeBalanceTotal),
            'integral'=>floatval($userInfo['integral']),
            'balance'=>floatval($userInfo['balance']),
            'nickname'=>$userInfo['nickname'],
            'avatar'=>$userInfo['avatar'],
        ];
        return $data;
    }


    public static function addUserIntegral($user_id,$action)
    {
        if($action=='1'){
            $isCheckIn = self::isCheckIn($user_id);
            if($isCheckIn){
                return true;
            }
            $dataIntegra = [
                'use_action'=>1,
                'type'=>1,
                'amount'=>1,
                'user'=>$user_id,
                'remark'=>'签到',
            ];
            return self::integraOrBalanceLog($dataIntegra,$type='2',$sign='1');
        }

        if($action=='2'){
            $isShare = self::isShare($user_id);
            self::where('id',$user_id)->setInc('share',1);
            if($isShare){
                return true;
            }
            $dataIntegra = [
                'use_action'=>2,
                'type'=>1,
                'amount'=>1,
                'user'=>$user_id,
                'remark'=>'分享',
            ];
            return self::integraOrBalanceLog($dataIntegra,$type='2',$sign='1');
        }
    }






    private static function isCheckIn($user_id){
        $now_time = GetMkTime(timeToStr(time(),'Y-m-d'));// 当天凌晨时间戳
        $isCheckIn = IntegralLog::where([
            'user'=>$user_id,
            'use_action'=>1
        ])->where('create_time','>',$now_time)
            ->find();
        if($isCheckIn)
            return true;
        return false;
    }


    private static function isShare($user_id){
        $now_time = GetMkTime(timeToStr(time(),'Y-m-d'));
        $isShare = IntegralLog::where([
            'user'=>$user_id,
            'use_action'=>2
        ])->where('create_time','>',$now_time)
            ->find();
        if($isShare)
            return true;
        return false;
    }


}
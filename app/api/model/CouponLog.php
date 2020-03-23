<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/26
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


class CouponLog extends BaseModel
{
    public function getCouponAttr($value)
    {
        $coupon = Coupon::where('id', $value)->field(['limit_user', 'number', 'type', 'integral', 'balance', 'limit_number', 'sort', 'status', 'create_time', 'update_time', 'delete_time'], true)->find();
        return $coupon;
    }

    public function getStatusAttr($value, $data)
    {
        $coupon_id = $data['coupon'];
        $coupon = Coupon::where('id', $coupon_id)->field(['limit_user', 'number', 'type', 'integral', 'balance', 'limit_number', 'sort', 'status', 'create_time', 'update_time', 'delete_time'], true)->find();
        if ($coupon['limit_time_type'] == 1) {// 按天计算 有效期
            $day = intval($coupon['day']['day']); // 领取后多少天有效 -1 要不要加
            $receive_time = intval($data['create_time']);// 领取时间  用户的领取时间
            $time_str = $day*86400;
            $end_time = $time_str+$receive_time;

            if (time() > $end_time and $value == 0) {
                $msg = '已失效';
            } else if ($value == 1) {
                $msg = '已使用';
            } else {
                $msg = '未使用';
            }
            return [
                'status' => $value,
                'end_time' => '截止' . timeToStr($end_time, 'Y-m-d'),
                'msg' => $msg
            ];
        }
        if ($coupon['limit_time_type'] == 2) { // 按时间范围
            if (time() > GetMkTime($coupon['end_time']) and $value == 0) {
                $msg = '已失效';
            } else if ($value == 1) {
                $msg = '已使用';
            } else {
                $msg = '未使用';
            }
            return [
                'status' => $value,
                'end_time' => $coupon['start_time'] . ' 至 ' . $coupon['end_time'],
                'msg' => $msg
            ];
        }
    }


    public static function userCoupon($user_id, $type)
    {
        $result = CouponLog::where('user', $user_id)
            ->order('create_time desc')
            ->where('status', $type)
            ->field(['user', 'use_time', 'delete_time', 'update_time'], true)->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', count($result));
    }


    public static function userAvailableCoupon($user_id, $merchant)
    {
        $result = CouponLog::where('user', $user_id)
            ->where('status', 0)
            ->field(['user', 'use_time', 'delete_time', 'update_time'], true)->select();
        $ids = [];
        foreach ($result as $k => $v) {
            if ($v['coupon']['merchant']['id'] == $merchant) {
                if ($v['status']['msg'] == '未使用') {
                    array_push($ids, $v['id']);
                }
            }
        }
        $result = CouponLog::where('user', $user_id)
            ->where('id', 'in', $ids)
            ->field(['user', 'use_time', 'delete_time', 'update_time'], true)->select();
        $merchant = Merchant::get($merchant);
        $proportion = $merchant['proportion'];// 积分抵扣多少元钱
        $user = User::where('id', $user_id)->field(['integral', 'balance'])->find();
        if($proportion > 0){
            $point_deduction = $user['integral'] / $proportion;
        }else{
            $point_deduction = $proportion;
        }

        $user['point_deduction'] = number_format($point_deduction,2,'.','');
        $merchant = [
            'title' => $merchant['title'],
            'id'=>$merchant['id']
        ];
        $data = [
            'coupon' => $result,
            'user' => $user,
            'merchant' => $merchant
        ];
        return $data;
    }

    public static function selectCoupon($user_id, $merchant, $total)
    {
        $result = CouponLog::where('user', $user_id)
            ->where('status', 0)
            ->order('create_time desc')
            ->field(['user', 'use_time', 'delete_time', 'update_time'], true)->select();
        //echo \json_encode($result);exit();
        $newResult = [];
        foreach ($result as $k => $v) {
            if ($v['coupon']['merchant']['id'] == $merchant) {
                //echo \json_encode($v);
                if ($v['status']['msg'] == '未使用') {
                    //echo \json_encode($v);
                    if (floatval($v['coupon']['use_condition']['amount']) == 0 || floatval($total) >= floatval($v['coupon']['use_condition']['amount'])) {
                        $result[$k]['disable'] = '1';
                        array_push($newResult, $result[$k]);
                    } else if (floatval($total) < floatval($v['coupon']['use_condition']['amount'])) {
                        $result[$k]['disable'] = '2';
                        array_push($newResult, $result[$k]);
                    } else {

                    }
                }
            }
        }
        //echo \json_encode($newResult);exit();
        //exit();
        return $newResult;
    }

}
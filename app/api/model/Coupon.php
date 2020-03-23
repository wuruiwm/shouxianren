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


class Coupon extends BaseModel
{
    public function getMerchantAttr($value)
    {
        return Merchant::readMerchant($value);
    }

    public function getDayAttr($value)
    {
        if ($value > 0) {
            return [
                'day' => $value,
                'str' => '领取后' . $value . '天内有效'
            ];
        } else {
            return '';
        }
    }

    public function getStartTimeAttr($value)
    {
        if (!empty($value)) {
            return timeToStr($value, 'Y-m-d');
        }
        return '';
    }

    public function getEndTimeAttr($value)
    {
        if (!empty($value)) {
            return timeToStr($value, 'Y-m-d');
        }
        return '';
    }

    public function getUseConditionAttr($value, $data)
    {
        if ($value > 0) {
            return [
                'amount' => floatval($value),
                'msg' => '消费满' . floatval($value) . '元减' . floatval($data['face_value']) . '元'
            ];
        } else {
            return [
                'amount' => $value,
                'msg' => ''
            ];
        }
    }

    public function couponLog()
    {
        return $this->hasMany('couponLog', 'coupon_id', 'id')->field(['user', 'coupon_id'], false);
    }


    public static function ttt()
    {
        $self = new Coupon();
        $result = $self->order('id desc')
            ->with('couponLog')
            ->select();
        return $result;
    }


    public static function readCoupon($action, $user_id)
    {
        if ($action == 1) {
            $sign = '=';
        } else {
            $sign = '<>';
        }
        $data = self::where('type', $sign, 1)
            ->select();
        //var_dump($data);exit();
        $ids = self::couponIds($data, $user_id);
        $result = self::order('sort desc,create_time desc')
            ->where('status', 0)
            ->where('type','<>', 5)
            ->order('create_time desc')
            ->with('couponLog')
            ->field(['limit_user', 'sort', 'status', 'create_time', 'update_time', 'delete_time'], true)
            ->where('id', 'in', $ids)->select();
        $result = self::handleData($result,$user_id);
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', count($result));
    }
    public static function readCouponnew($action, $user_id,$merchant)
    {
        if ($action == 1) {
            $sign = '=';
        } else {
            $sign = '<>';
        }
        $data = self::where('type', $sign, 1)
            ->select();
        $ids = self::couponIds($data, $user_id);
        $result = self::order('sort desc,create_time desc')
            ->where('status', 0)
            ->where('type','<>', 5)
          	->where('merchant',$merchant)
            ->order('create_time desc')
            ->with('couponLog')
            ->field(['limit_user', 'sort', 'status', 'create_time', 'update_time', 'delete_time'], true)
            ->where('id', 'in', $ids)->select();
        $result = self::handleData($result,$user_id);
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', count($result));
    }


    private static function couponIds($data, $user_id)
    {
        $ids = [];
        foreach ($data as $k => $v) {
            if ($v['limit_user'] == '') {
                array_push($ids, $v['id']);
            } else {
                if (strpos($v['limit_user'], ',') !== false) {
                    $limit_number_data = explode(',', $v['limit_user']);
                    if (in_array($user_id, $limit_number_data))
                        array_push($ids, $v['id']);
                } else {
                    if ($v['limit_user'] == $user_id)
                        array_push($ids, $v['id']);
                }
            }
        }
        return $ids;
    }

    private static function handleData($result,$user_id){
        foreach ($result as $k1 => $v1) {
            if (!empty($v1['coupon_log'])) {
                $num = 0;
                $total_receive_number = 0;
                foreach ($v1['coupon_log'] as $k2 => $v2) {
                    if ($v2['user'] == $user_id) {
                        $num = $num + 1;
                    }
                    $result[$k1]['receive_number'] = $num;
                    $total_receive_number = $total_receive_number+1;
                }
                if($total_receive_number >= $v1['number'] ){
                    unset($result[$k1]);
                }

            } else {
                $result[$k1]['receive_number'] = 0;
            }


            if($v1['limit_number']!==0){
                if($v1['receive_number'] >= $v1['limit_number']){
                    unset($result[$k1]);
                }
            }

            if(!empty($v1['end_time'])){
                $end_time = GetMkTime($v1['end_time']);
                if($end_time <= time()){
                    unset($result[$k1]);
                }
            }

            unset($result[$k1]['coupon_log']);


        }
        return array_values($result);
    }
}
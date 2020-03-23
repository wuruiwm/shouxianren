<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/30
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


class ConsumptionOrder extends BaseModel
{
    public function getMerchantAttr($value)
    {
        return Merchant::readMerchant($value);
    }

    public function getUserAttr($value)
    {
        $result = User::exist('id', $value, ['avatar', 'nickname'], false);
        $result['user'] = $value;
        return $result;
    }

    public function getCouponAttr($value)
    {
        $result = Coupon::where('id', $value)->find();
        if ($result) {
            return [
                'id' => $value,
                'title' => $result['title'],
                'face_value'=>$result['face_value']
            ];
        }
        return [];
    }

    public static function getList($page, $limit, $user_id)
    {
        $number = $page * $limit;
        $self = new ConsumptionOrder();
        $result = $self::limit($number, $limit)
            ->where('merchant_user', $user_id)
            ->order('create_time desc')
            ->field(['merchant', 'merchant_user', 'real_payment', 'coupon_user'], true)
            ->select();
        $data['today']=number_format(self::getRangeTotalAmount($user_id,'today'),2,".","");
        $data['yesterday']=number_format(self::getRangeTotalAmount($user_id,'yesterday'),2,".","");
        $data['week']=number_format(self::getRangeTotalAmount($user_id,'week'),2,".","");
        $data['last_week']=number_format(self::getRangeTotalAmount($user_id,'last week'),2,".","");
        $data['month']=number_format(self::getRangeTotalAmount($user_id,'month'),2,".","");
        $data['last_month']=number_format(self::getRangeTotalAmount($user_id,'last month'),2,".","");
        $data['year']=number_format(self::getRangeTotalAmount($user_id,'year'),2,".","");
        $data['last_year']=number_format(self::getRangeTotalAmount($user_id,'last_year'),2,".","");
        $resultData = [
            'sales_volume'=>$data,
            'order'=>$result
        ];
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($resultData, 0, 'ok', self::count());
    }

    public static function getRangeTotalAmount($user_id,$where)
    {
        $result = self::where('merchant_user', $user_id)
            ->whereTime('create_time',$where)
            ->order('create_time desc')
            ->field(['merchant', 'merchant_user', 'real_payment', 'coupon_user'], true)
            ->sum('total_amount');
        return $result;
    }

    public static function getListByUser($user_id)
    {
        return self::where('user',$user_id)->where('status',1)->order('create_time desc')->select();
    }
}
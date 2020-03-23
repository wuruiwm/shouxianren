<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/30
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


class ConsumptionOrder extends BaseModel
{
    public function getUserAttr($value)
    {
        $result = User::where('id', $value)->field(['avatar', 'nickname', 'mobile'], false)->find();
        return $result;
    }

    public function getCouponAttr($value)
    {
        $result = Coupon::where('id', $value)->field(['title', 'face_value'], false)->find();
        return $result;
    }

    public function getMerchantAttr($value)
    {
        return Merchant::getMerchantById($value);
    }

    public static function getList($page, $limit, $key)
    {
        if (isset($key['order']) and !empty($key['order'])) {
            $where['order'] = ['like', '%' . $key['order'] . '%'];
        }
        if (isset($key['merchant']) and !empty($key['merchant'])) {
            $where['merchant'] = $key['merchant'];
        }
        if (isset($key['time']) and !empty($key['time'])) {
            $time = explode(" - ", $key['time']);
            $start_time = GetMkTime($time[0]);
            $end_time = GetMkTime($time[1]);
            $where['create_time'] = ['between', "$start_time,$end_time"];
        }
        if (isset($key['mobile']) and !empty($key['mobile'])) {
            $userInfo = User::where(['mobile' => ['like', '%' . $key['mobile']]])->find();
            $where['user'] = $userInfo['id'];
        }
        if (empty($where['order']) and empty($where['merchant']) and empty($where['create_time']) and empty($where['user'])) {
            $where = null;
        }
        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->order('create_time desc')
            ->where($where)
            ->where('status',1)
            ->select();
        //showjson($result);
        foreach($result as $k=>$v){
            if($v['merchant_integral_deduction']!==0){
                $integral =  $v['integral'] / $v['merchant_integral_deduction'];
                $result[$k]['merchant_integral_deduction_copy'] = number_format($integral,2,'.','');
            }
            if(empty($result[$k]['merchant_integral_deduction_copy'])){
                $result[$k]['merchant_integral_deduction_copy'] = '0.00';
            }
        }

        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }

    public static function getExportList($ids)
    {
        if(empty($ids)){
            $self = new ConsumptionOrder();
            $result = $self->order('create_time desc')
                ->select();
        }else{
            $self = new ConsumptionOrder();
            $result = $self->order('create_time desc')
                ->where('id','in',$ids)
                ->select();
        }

        $result = self::handleData($result);
        return $result;
    }
  	
    public static function getExportListAide($merchant,$mobile,$order,$time)
    {
        if (!empty($merchant)) {
            $where['merchant'] = $merchant;
        }

        if (!empty($mobile)) {
            $userInfo = User::where(['mobile' => ['like', '%' . $mobile]])->find();
            $where['user'] = $userInfo['id'];
        }

        if (!empty($order)) {
            $where['order'] = $order;
        }

        if (!empty($time)) {
            $time = explode(" - ", $time);
            $start_time = GetMkTime($time[0]);
            $end_time = GetMkTime($time[1]);
            $where['create_time'] = ['between', "$start_time,$end_time"];
        }


        if ( empty($merchant) and empty($mobile) and empty($order) and empty($time) ) {
            $where = null;
        }

        $self = new ConsumptionOrder();
        $result = $self->where($where)
//            ->where('status',1)
            ->order('create_time desc')
            ->select();
        $result = self::handleData($result);
        return $result;
    }

    private static function handleData($result)
    {
        foreach ($result as $k => $v) {
            $result[$k]['merchant_copy'] = $v['merchant']['title'];
            $result[$k]['user_copy'] = $v['user']['nickname'].'：'.$v['user']['mobile'];
            if(!empty($v['coupon'])){
//                $result[$k]['coupon_copy'] = $v['coupon']['title'].'，面值'.$v['coupon']['face_value'].'元';
                $result[$k]['coupon_copy'] = $v['coupon']['title'];
                $result[$k]['coupon_copy1'] = $v['coupon']['face_value'];
            }else{
                $result[$k]['coupon_copy'] = '暂无使用任何优惠券';
                $result[$k]['coupon_copy1'] = 0;
            }

            if($v['status']==1){
                $result[$k]['status'] = '已支付';
            }else{
                $result[$k]['status'] = '待支付';
            }

            if($v['pay_type']==1){
                $result[$k]['pay_type'] = '微信';
            }else if($v['pay_type']==2){
                $result[$k]['pay_type'] = '支付宝';
            }else{
                $result[$k]['pay_type'] = '余额';
            }

            if($v['merchant_integral_deduction']!==0){
                $integral =  $v['integral'] / $v['merchant_integral_deduction'];
                $result[$k]['merchant_integral_deduction_copy'] = number_format($integral,2,'.','');
            }else{
                $result[$k]['merchant_integral_deduction_copy'] = 0;
            }
        }

        return $result;
    }

}
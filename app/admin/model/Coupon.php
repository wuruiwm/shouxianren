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

namespace app\admin\model;


class Coupon extends BaseModel
{
    public function getMerchantAttr($value)
    {
        return Merchant::getMerchantById($value);
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
        return $this->hasMany('CouponLog', 'coupon', 'id');
    }

    public static function createOrUpdateCoupon($data, $id = false)
    {
        $self = new Coupon();
        if ($id) {
            $result = $self->allowField(true)->save($data, ['id' => $id]);
        } else {
            $result = $self->allowField(true)->save($data);
        }
        if ($result) {
            return true;
        }
        return false;
    }

    public static function getList($page, $limit, $key)
    {
        if (isset($key['title']) and !empty($key['title'])) {
            $where['title'] = ['like', '%' . $key['title'] . '%'];
        }
        if (isset($key['merchant']) and !empty($key['merchant'])) {
            $where['merchant'] = $key['merchant'];
        }
        if (isset($key['status']) and !empty($key['status'])) {
            $where['status'] = $key['status'];
        }
        if (empty($where['title']) and empty($where['merchant']) and empty($where['status'])) {
            $where = null;
        }

        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->order('id desc')
            ->where($where)
            ->with('couponLog')
            ->select();
        $result = self::handleData($result);

        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }

    public static function getInfoById($id)
    {
        return self::get($id);
    }

    public static function updateById($id, $data)
    {
        $self = new Coupon();
        //echo \json_encode($data);exit();
        return $self->allowField(true)->save($data, ['id' => $id]);
    }


    public static function getExportList()
    {
        $self = new Coupon();
        $result = $self->order('id desc')
            ->with('couponLog')
            ->select();
        $result = self::handleData($result);
        return $result;
    }


    private static function handleData($result){
        foreach ($result as $k => $v) {
            if (!empty($_log = $v['coupon_log'])) {
                foreach ($_log as $m => $n) {
                    if ($n['status'] == 1) {
                        if (!isset($num)) {
                            $num = 1;
                        } else {
                            $num += 1;
                            $result[$k]['use_number'] = $num;
                        }
                    } else {
                        if (!isset($result[$k]['use_number'])) {
                            $result[$k]['use_number'] = 0;
                        } else {

                        }
                    }
                }
                $result[$k]['receive'] = count($v['coupon_log']);
                $result[$k]['surplus'] = $v['number'] - count($v['coupon_log']);

            } else {
                $result[$k]['receive'] = 0;
                $result[$k]['surplus'] = $v['number'] - 0;
                $result[$k]['use_number'] = 0;
            }
        }
        return $result;
    }


}
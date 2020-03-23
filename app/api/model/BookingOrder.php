<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/25
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


class BookingOrder extends BaseModel
{
    protected $hidden = ['handle_time', 'update_time', 'delete_time'];

    public function getMerchantAttr($value)
    {
        $result = Merchant::get($value);
        return $result['title'];
    }

    public function getActionAttr($value)
    {
        $result = Action::get($value);
        return [
            'id' => $result['id'],
            'title' => $result['title'],
            'img' => $result['head_img']['data'][0]['filepath']
        ];
    }

    public function getUserAttr($value)
    {
        $result = \app\api\model\User::get($value);
        return [
            'id' => $result['id'],
            'avatar' => $result['avatar']['filepath'],
            'nickname' => $result['nickname']
        ];
    }

    public function getCreateTimeAttr($value)
    {
        return timeToStr(GetMkTime($value), 'Y-m-d H:i');
    }


    public static function createBookingOrder($data)
    {
        return self::create($data);
    }

    public static function exist($key, $value, $except = [], $boolean = true)
    {
        return self::field($except, $boolean)->where([$key => $value])->find();
    }
}
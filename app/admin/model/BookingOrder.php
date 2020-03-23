<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/25
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


class BookingOrder extends BaseModel
{
    public function getHandleTimeAttr($value)
    {
        if(!empty($value)){
            return timeToStr(GetMkTime($value), 'Y-m-d H:i:s');
        }
    }
  
    public function getMerchantAttr($value)
    {
        $result = Merchant::get($value);
        return $result['title'];
    }

    public function getActionAttr($value)
    {
        $result = Action::get($value);
        return $result['title'];
    }

    public function getUserAttr($value)
    {
        $result = \app\api\model\User::get($value);
        return [
            'avatar' => $result['avatar']['filepath'],
            'nickname' => $result['nickname'],
            'mobile' => $result['mobile']
        ];
    }

    public function getStatusAttr($value)
    {
        return ['<span style="color: #ff851a">待处理</span>','<span style="color: #23ff08">已处理</span>'][$value];
    }
    public static function readBookingOrder($page,$limit,$key){
        if (isset($key['merchant']) and !empty($key['merchant'])) {
            $where['merchant'] = $key['merchant'];
        }
        if (empty($where['merchant'])) {
            $where = null;
        }

        $number = $page * $limit;
        $banner = self::limit($number, $limit)
            ->order('id desc')
            ->where($where)
            ->select();
        if (!$banner) {
            return self::dataFormat(0);
        }
        return self::dataFormat($banner, 0, 'ok', self::count());
    }

}
<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/17
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


use app\lib\exception\SuccessMessage;

class MerchantCate extends BaseModel
{
    public static function createMerchantCate($data)
    {
        if(self::create($data))
            throw new SuccessMessage();
    }

    public static function deleteMerchantCate($id)
    {
        $result = self::destroy($id);
        if($result)
            throw new SuccessMessage();
    }

    public static function updateMerchantCate($id,$data)
    {
        $self = new MerchantCate();
        if($self->allowField(true)->save($data,['id'=>$id]))
            throw new SuccessMessage();
    }


    public static function readMerchantCate($page, $limit)
    {
        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->order('id desc')
            ->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }
}
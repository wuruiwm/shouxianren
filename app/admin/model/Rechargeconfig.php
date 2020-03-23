<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/11
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


use app\lib\exception\SuccessMessage;

class Rechargeconfig extends BaseModel
{
    public static function updateRechargeconfig($data,$id){
        $self = new Rechargeconfig();
        if($self->allowField(true)->save($data,['id'=>$id]))
            throw new SuccessMessage();

    }

    public static function readRechargeconfig($id)
    {
        return self::find($id);
    }
}
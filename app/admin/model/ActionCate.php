<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/23
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


class ActionCate extends BaseModel
{

    public static function createActionCate($data)
    {
        return self::create($data);
    }

    public static function deleteActionCate($id)
    {
        return self::destroy($id);
    }

    public static function updateActionCate($id,$data)
    {
        $self = new ActionCate();
        return $self->allowField(true)->save($data,['id'=>$id]);
    }

    public static function readActionCate($page, $limit)
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
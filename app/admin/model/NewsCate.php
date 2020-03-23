<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/9
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\lib\exception\SuccessMessage;

class NewsCate extends BaseModel
{
    public static function createNewsCate($data)
    {
        if(self::create($data))
            throw new SuccessMessage();
    }

    public static function deleteNewsCate($id)
    {
        $result = self::destroy($id);
        if($result)
            throw new SuccessMessage();
    }

    public static function updateNewsCate($id,$data)
    {
        $self = new NewsCate();
        if($self->allowField(true)->save($data,['id'=>$id]))
            throw new SuccessMessage();
    }

    public static function readNewsCate($page, $limit)
    {
        $number = $page * $limit;
        $banner = self::limit($number, $limit)
            ->order('id desc')
            ->select();
        if (!$banner) {
            return self::dataFormat(0);
        }
        return self::dataFormat($banner, 0, 'ok', self::count());
    }
}
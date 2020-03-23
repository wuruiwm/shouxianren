<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/31 21:49
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


use app\lib\exception\SuccessMessage;

class Message extends BaseModel
{
    public static function createMessage($post)
    {
        return self::create($post);
    }

    public static function updateMessage($id,$data)
    {
        $self = new Message();
        if($self->allowField(true)->save($data,['id'=>$id]))
            throw new SuccessMessage();
    }

    public static function readMessage($page, $limit)
    {
        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->order('create_time desc')
            ->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }

    public static function readMessageById($id){
        $result = self::get($id);
        return $result;
    }

    public static function deleteMessage($id)
    {
        $ids = explode(',',$id);
        self::destroy($ids);
        throw new SuccessMessage();
    }
}
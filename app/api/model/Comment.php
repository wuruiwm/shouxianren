<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/16
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


class Comment extends BaseModel
{

    protected $hidden = ['update_time', 'delete_time'];

    public function getUserAttr($value)
    {
        $result = User::exist('id', $value, ['avatar', 'nickname'], false);
        return $result;
    }

    public function getCreateTimeAttr($value)
    {
        return timeToStr(GetMkTime($value), 'm-d H:i');
    }


    public function getNewsCateAttr($value)
    {
        $title = NewsCate::find($value)['title'];
        return ['id' => $value, 'title' => $title];
    }


    public static function createComment($data)
    {
        if ($data['type'] == 1) {
            $self = new News();
            $self->where('id', $data['article'])->setInc('comment_num');
        }
        if ($data['type'] == 2) {
            $self = new Action();
            $self->where('id', $data['article'])->setInc('comment_num');
        }
        if ($data['type'] == 3) {
            $self = new Square();
            $self->where('id', $data['article'])->setInc('comment_num');
        }

        return self::create($data);
    }
}
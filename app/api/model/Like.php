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


class Like extends BaseModel
{
    protected $hidden = ['update_time', 'delete_time'];

    public function getUserAttr($value)
    {
        $result = User::exist('id', $value, ['avatar', 'nickname'], false);
        return $result;
    }

    public static function createLike($post)
    {
        if ($post['type'] == 1) {
            $self = new News();
            $self->where('id', $post['article'])->setInc('awesome_num');
        }
        if ($post['type'] == 2) {
            $self = new Action();
            $self->where('id', $post['article'])->setInc('awesome_num');
        }
        if ($post['type'] == 3) {
            $self = new Square();
            $self->where('id', $post['article'])->setInc('awesome_num');
        }
        return self::create($post);
    }

    public static function deleteLike($post)
    {
        $self = new Like();
        $result = $self::where('user', $post['user'])
            ->where('type', $post['type'])
            ->where('article', $post['article'])
            ->delete();
        if ($post['type'] == 1) {
            $self = new News();
            $self->where('id', $post['article'])->setDec('awesome_num');
        }
        if ($post['type'] == 2) {
            $self = new Action();
            $self->where('id', $post['article'])->setDec('awesome_num');
        }
        if ($post['type'] == 3) {
            $self = new Square();
            $self->where('id', $post['article'])->setDec('awesome_num');
        }
        return $result;
    }
}
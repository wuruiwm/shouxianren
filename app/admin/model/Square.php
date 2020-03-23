<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/6/3
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


class Square extends BaseModel
{

    public function getImgAttr($value)
    {
        if (!empty($value)) {
            $result = Attachment::where('id', 'in', $value)->field(['id', 'filepath'])->select();
            $qiniu = Qiniuconfig::get(1);
            $qiniu['prefix_url'];
            foreach ($result as $k => $v) {
                $result[$k]['filepath'] = $qiniu['prefix_url'] . '/' . $result[$k]['filepath'];
            }
            return $result;
        }
        return '';
    }

    public function getUserAttr($value)
    {
        $result = User::where('id', $value)->field(['avatar', 'nickname', 'mobile'], false)->find();
        return $result;
    }


    public static function readList($page = '', $limit = '', $key = '')
    {
        if (isset($key['title']) and !empty($key['title'])) {
            $where['title'] = $key['title'];
        }
        if (empty($where['title'])) {
            $where = null;
        }

        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->order('create_time desc')
            ->where($where)
            ->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }

    public static function readListByIdComment($page = '', $limit = '', $id = '')
    {
        $number = $page * $limit;
        $result = Comment::limit($number, $limit)
            ->order('create_time desc')
            ->where('article', $id)
            ->where('type', 3)
            ->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', count($result));
    }

    public static function lessCommentNumber($id,$count)
    {
        $self = new Square();
        return $self->where('id', $id)->setDec('comment_num',$count);
    }

    public static function deleteByIds($ids=null){
        $ids = explode(',', $ids);
        return self::destroy($ids);
    }
}
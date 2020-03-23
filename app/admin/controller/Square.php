<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/6/3
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\model\Comment;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;

class Square extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }

    public function viewcomment()
    {
        return $this->fetch();
    }

    public function readList($page = '', $limit = '', $key = '')
    {
        return \app\admin\model\Square::readList($page - 1, $limit, $key);
    }


    public function readListByIdComment($page = '', $limit = '', $id = '')
    {
        return \app\admin\model\Square::readListByIdComment($page - 1, $limit, $id);
    }

    public function deleteSquareCommentByIds($ids, $id)
    {
        $ids = explode(',', $ids);
        $count = count($ids);
        $result = \app\admin\model\Square::lessCommentNumber($id, $count);
        $result = Comment::deleteByIds($ids);
        if ($result)
            throw new SuccessMessage();
        throw new ErrorMessage();
    }

    public function deleteSquareByIds($ids)
    {
        // 以下是做检查 该圈信息下是否有相关评论，数据上更加严谨
//        $is_exist = Comment::where('article','in',$ids)
//            ->where('type',3)
//            ->count();
//        return $is_exist;
//        if($is_exist)
//            throw new ErrorMessage(['msg'=>'操作错误，请先删除选择行里的评论数据']);

        $result = \app\admin\model\Square::deleteByIds($ids);
        if($result)
            throw new SuccessMessage();
        throw new ErrorMessage(['msg'=>'操作失败，请稍后重试']);
    }

}
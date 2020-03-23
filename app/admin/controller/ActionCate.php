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

namespace app\admin\controller;


use app\admin\model\Action;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\NewsCate;

class ActionCate extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }


    public function publish()
    {
        return $this->fetch();
    }


    public function createActionCate()
    {
        (new NewsCate())->goCheck();
        $result = \app\admin\model\ActionCate::createActionCate(input('post.'));
        if($result)
            throw new SuccessMessage();
    }

    public function deleteActionCate($id)
    {
        $result = Action::where('action_cate',$id)->select();
        if(count($result)>0)
            throw new ErrorMessage([
                'msg'=>'该栏目下有内容不能被删除'
            ]);
        $result = \app\admin\model\ActionCate::deleteActionCate($id);
        if($result)
            throw new SuccessMessage();
    }

    public function updateActionCate($id){
        $result = \app\admin\model\ActionCate::updateActionCate($id,input('post.'));
        if($result)
            throw new SuccessMessage();
    }

    public function readActionCate($page = '', $limit = '')
    {
        return \app\admin\model\ActionCate::readActionCate($page - 1, $limit);
    }
}
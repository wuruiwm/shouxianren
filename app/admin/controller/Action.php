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


use app\admin\model\ActionCate;
use app\admin\model\Comment;
use app\admin\model\Merchant;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\News;

class Action extends Permissions
{
    public function index()
    {
        $result = ActionCate::all();
        $this->assign('cate',$result);

        $merchant = Merchant::all();
        $this->assign('merchant',$merchant);
        return $this->fetch('action/index');
    }


    public function publish()
    {
        $merchant = Merchant::all();
        $result = ActionCate::all();
        $this->assign('cate',$result);
        $this->assign('merchant',$merchant);

        return $this->fetch();
    }

    public function viewcomment()
    {
        return $this->fetch();
    }

    public function update()
    {
        $merchant = Merchant::all();
        $result = ActionCate::all();
        $this->assign('cate',$result);
        $this->assign('merchant',$merchant);
        return $this->fetch();
    }


    public function createAction()
    {
        (new News())->goCheck();
        if(input('post.news_type')==1){
            if(empty(input('post.video_id'))){
                throw new ErrorMessage([
                    'msg'=>'请先上传视频文件'
                ]);
            }
        }
        unset(input('post.')['file']);
        $post = input('post.');
        $content = str_replace("<embed","<video",$post['content']);
        $content = str_replace("embed>","video>",$content);
        $post['content'] = $content;
        return \app\admin\model\Action::createAction($post);
    }

    public function deleteAction($id='')
    {
        return \app\admin\model\Action::deleteAction($id);
    }

    public function updateAction($id){
        $post = input('post.');
        $content = str_replace("<embed","<video",$post['content']);
        $content = str_replace("embed>","video>",$content);
        $post['content'] = $content;
        return \app\admin\model\Action::updateAction($id,$post);
    }

    public function readAction($page = '', $limit = '',$key='')
    {
        return \app\admin\model\Action::readAction($page - 1, $limit,$key);
    }

    public function readActionById($id=''){
        return \app\admin\model\Action::readActionById($id);
    }

    public function readListByIdComment($page = '', $limit = '', $id = '')
    {
        return \app\admin\model\Action::readListByIdComment($page - 1, $limit, $id);
    }

    public function deleteActionCommentByIds($ids, $id)
    {
        $ids = explode(',', $ids);
        $count = count($ids);
        $result = \app\admin\model\Action::lessCommentNumber($id, $count);
        $result = Comment::deleteByIds($ids);
        if ($result)
            throw new SuccessMessage();
        throw new ErrorMessage();
    }

}
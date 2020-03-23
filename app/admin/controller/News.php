<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/9
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Comment;
use \app\admin\model\News as newsModel;
use app\admin\model\NewsCate;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use \app\lib\validate\News as newsValidate;


class News extends Permissions
{
    public function index()
    {
        $result = NewsCate::all();
        $this->assign('cate',$result);
        return $this->fetch('news/index');
    }


    public function publish()
    {
        $result = NewsCate::all();
        $this->assign('cate',$result);
        return $this->fetch();
    }

    public function update()
    {
        $result = NewsCate::all();
        $this->assign('cate',$result);
        return $this->fetch();
    }

    public function viewcomment()
    {
        return $this->fetch();
    }


    public function createNews()
    {
        (new newsValidate())->goCheck();
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
        return newsModel::createNews($post);
    }

    public function deleteNews($id='')
    {
        return newsModel::deleteNews($id);
    }

    public function updateNews($id){
        $post = input('post.');
        $content = str_replace("<embed","<video",$post['content']);
        $content = str_replace("embed>","video>",$content);
        $post['content'] = $content;
        return newsModel::updateNews($id,$post);
    }

    public function readNews($page = '', $limit = '',$key='')
    {
        return newsModel::readNews($page - 1, $limit,$key);
    }

    public function readNewsById($id=''){
        return newsModel::readNewsById($id);
    }

    public function readListByIdComment($page = '', $limit = '', $id = '')
    {
        return newsModel::readListByIdComment($page - 1, $limit, $id);
    }

    public function deleteNewsCommentByIds($ids, $id)
    {
        $ids = explode(',', $ids);
        $count = count($ids);
        $result = newsModel::lessCommentNumber($id, $count);
        $result = Comment::deleteByIds($ids);
        if ($result)
            throw new SuccessMessage();
        throw new ErrorMessage();
    }
}
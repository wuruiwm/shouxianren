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

namespace app\admin\controller;

use app\admin\model\News;
use app\admin\model\NewsCate as NewsCateModel;
use app\lib\exception\ErrorMessage;
use app\lib\validate\NewsCate as NewsCateValidate;

class NewsCate extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }


    public function publish()
    {
        return $this->fetch();
    }


    public function createNewsCate()
    {
        (new NewsCateValidate())->goCheck();
        return NewsCateModel::createNewsCate(input('post.'));
    }

    public function deleteNewsCate($id=1)
    {
        $result = News::where('news_cate',$id)->select();
        if(count($result)>0)
            throw new ErrorMessage([
                'msg'=>'该栏目下有内容不能被删除'
            ]);
        return NewsCateModel::deleteNewsCate($id);
    }

    public function updateNewsCate($id){
        return NewsCateModel::updateNewsCate($id,input('post.'));
    }

    public function readNewsCate($page = '', $limit = '')
    {
        return NewsCateModel::readNewsCate($page - 1, $limit);
    }

}
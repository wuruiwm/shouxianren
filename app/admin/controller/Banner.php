<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/7
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\lib\exception\SuccessMessage;
use \app\admin\model\Banner as BannerModel;
use \app\lib\validate\Banner as BannerValidate;


class Banner extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }


    public function publish()
    {
        return $this->fetch();
    }

    public function update()
    {
        return $this->fetch();
    }

    public function createBanner()
    {
        (new BannerValidate())->goCheck();
        return BannerModel::createBanner(input('post.'));

    }

    public function deleteBanner($id){
        return BannerModel::deleteBanner($id);
    }

    public function updateBanner($id){
        return BannerModel::updateBanner($id,input('post.'));
    }

    public function readBanner($page = '', $limit = '',$key='')
    {
        return BannerModel::readBanner($page - 1, $limit,$key);
    }

    public function readBannerById($id=''){
        return BannerModel::readBannerById($id);
    }
}
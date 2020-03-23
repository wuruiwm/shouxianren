<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/17
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\model\Merchant;

class MerchantCate extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }

    public function publish()
    {
        return $this->fetch();
    }


    public function createMerchantCate()
    {
        return \app\admin\model\MerchantCate::createMerchantCate(input('post.'));
    }

    public function deleteMerchantCate($id=1)
    {
        $result = Merchant::where('type',$id)->select();
        if(count($result)>0)
            throw new ErrorMessage([
                'msg'=>'该类型下有商家不能被删除'
            ]);
        return \app\admin\model\MerchantCate::deleteMerchantCate($id);
    }

    public function updateMerchantCate($id){
        return \app\admin\model\MerchantCate::updateMerchantCate($id,input('post.'));
    }

    public function readMerchantCate($page = '', $limit = '')
    {
        return \app\admin\model\MerchantCate::readMerchantCate($page - 1, $limit);
    }
}
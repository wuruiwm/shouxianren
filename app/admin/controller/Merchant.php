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

// 商户
use app\admin\model\MerchantCate;
use app\lib\exception\SuccessMessage;

class Merchant extends Permissions
{
    public function index(){
        return $this->fetch();
    }
    public function update()
    {
        $result = MerchantCate::all();
        $this->assign('cate',$result);
        return $this->fetch();
    }

    public function readMerchant($page = '', $limit = '',$key='')
    {
        return \app\admin\model\Merchant::readMerchant($page - 1, $limit,$key);
    }

    public function updateMerchant($id){
        (new \app\lib\validate\Merchant())->goCheck();
        return \app\admin\model\Merchant::updateMerchant($id,input('post.'));
    }

    public function readMerchantById($id=''){
        return \app\admin\model\Merchant::readMerchantById($id);
    }

    public function qrcode($id){
        $result = \app\admin\model\Merchant::qrcode($id);
        throw new SuccessMessage([
            'data'=>['qrcode'=>$result]
        ]);
    }
}
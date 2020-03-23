<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/25
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\model\Merchant;
use app\lib\validate\ValidPage;

class BookingOrder extends Permissions
{
    public function index(){
        $merchant = Merchant::all();
        $this->assign('merchant',$merchant);
        return $this->fetch();
    }

    public function readBookingOrder($page = '', $limit = '',$key=''){
        (new ValidPage())->goCheck();
        return \app\admin\model\BookingOrder::readBookingOrder($page-1,$limit,$key);
    }
}
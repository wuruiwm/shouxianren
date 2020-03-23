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


use app\lib\exception\SuccessMessage;

class Rechargeconfig extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }


    public function updateRechargeconfig(){
        if(input('post.integral')==0 || input('post.balance')==0){

        }else{
            (new \app\lib\validate\Rechargeconfig())->goCheck();
        }
        return \app\admin\model\Rechargeconfig::updateRechargeconfig(input('post.'),$id=1);
    }

    public function readRechargeconfig()
    {
        $result =  \app\admin\model\Rechargeconfig::readRechargeconfig($id=1);
        throw new SuccessMessage([
            'data'=>$result
        ]);
    }
}
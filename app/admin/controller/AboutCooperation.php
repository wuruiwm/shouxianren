<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/22
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\lib\exception\SuccessMessage;

class AboutCooperation extends Permissions
{
    public function index(){
        return $this->fetch();
    }

    public function updateAboutCooperation(){
        $result = \app\admin\model\AboutCooperation::updateAboutCooperation(input('post.'),$id=1);
        if($result)
            throw new SuccessMessage();

    }

    public function readAboutCooperation()
    {
        $result =  \app\admin\model\AboutCooperation::readAboutCooperation($id=1);
        throw new SuccessMessage([
            'data'=>$result
        ]);
    }
}
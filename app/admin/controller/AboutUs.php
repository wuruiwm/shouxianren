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

class AboutUs extends Permissions
{
    public function index(){
        return $this->fetch();
    }

    public function updateAboutUs(){
       $result = \app\admin\model\AboutUs::updateAboutUs(input('post.'),$id=1);
        if($result)
            throw new SuccessMessage();

    }

    public function readAboutUs()
    {
        $result =  \app\admin\model\AboutUs::readAboutUs($id=1);
        throw new SuccessMessage([
            'data'=>$result
        ]);
    }

}
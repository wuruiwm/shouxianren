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

namespace app\api\controller\v1;


use app\api\model\AboutCooperation;
use app\api\model\AboutUs;
use app\lib\exception\SuccessMessage;

class About extends BaseController
{
    /**
     * @apiDefine  about 其他
     */

    /**
     * @api {get} about/us  1、关于我们
     * @apiGroup about
     * @apiVersion 0.1.0
     * @apiDescription  关于我们
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/about/us
     * @apiName 16
     */
    public function readAboutUs(){
        $result = AboutUs::get(1);
        throw new SuccessMessage([
            'data'=>$result
        ]);
    }


    /**
     * @api {get} about/cooperation  2、商务合作
     * @apiGroup about
     * @apiVersion 0.1.0
     * @apiDescription  商务合作
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/about/cooperation
     * @apiName 17
     */
    public function readAboutCooperation(){
        $result = AboutCooperation::get(1);
        throw new SuccessMessage([
            'data'=>$result
        ]);
    }
}
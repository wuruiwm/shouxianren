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

namespace app\api\controller\v1;
use \app\api\model\Banner as BannerModel;

class Banner extends BaseController
{
    protected $beforeActionList = [
        'mustBeGet' => ['only' => 'readbanner'],
        'mustBePost' => ['only' => ''],
    ];

    /**
     * @apiDefine  banner 轮播图
     */

    /**
     * @api {get} banner/read 1、所有轮播列表
     * @apiGroup banner
     * @apiVersion 0.1.0
     * @apiDescription  点击轮播左上角更多进入轮播图列表页
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/banner/read
     * @apiName 1
     */

    /**
     * @api {get} banner/read?type_id=1 2、指定位置轮播列表
     * @apiGroup banner
     * @apiVersion 0.1.0
     * @apiDescription  根据type_id 获取制定位置轮播图列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/banner/read?type_id=1
     * @apiParam  {int} type_id='' 位置类型ID
     * @apiName 2
     */

    public function readBanner($type_id=''){
        return BannerModel::readBanner($type_id);
    }

}
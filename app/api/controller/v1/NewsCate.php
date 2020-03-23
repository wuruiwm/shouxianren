<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/14
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;

use \app\api\model\NewsCate as newsCateModel;

class NewsCate extends BaseController
{

    /**
     * @api {get} newscate/read  4、所有新闻类型
     * @apiGroup news
     * @apiVersion 0.1.0
     * @apiDescription  所有新闻类型
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/newscate/read
     * @apiName 11
     */
    public function readNewsCate()
    {
        return newsCateModel::readNewsCate();
    }


}
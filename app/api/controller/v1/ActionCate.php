<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/23
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\lib\exception\SuccessMessage;

class ActionCate extends BaseController
{
    protected $beforeActionList = [
        'mustBeGet' => ['only' => 'readactioncate'],
        'mustBePost' => ['only' => ''],
    ];
    /**
     * @apiDefine  action 活动
     */

    /**
     * @api {get} actioncate/read  1、所有栏目类型
     * @apiGroup action
     * @apiVersion 0.1.0
     * @apiDescription  所有栏目类型，除首页外[文化旅游,美食特产]的栏目
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/actioncate/read
     * @apiName 19
     */
    public function readActionCate()
    {
        $result = \app\api\model\ActionCate::readActionCate();
        throw new SuccessMessage([
            'data'=>$result
        ]);
    }
}
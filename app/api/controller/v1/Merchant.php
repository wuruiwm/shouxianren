<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/28
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\api\model\ConsumptionOrder;
use app\api\service\Token;
use app\lib\validate\ValidPage;

class Merchant extends BaseController
{
    /**
     * @api {get} merchant/order  16、商家核销订单
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  个人中心，商家订单里的 核销订单 包含(业绩+订单详情)
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/merchant/order
     * @apiParam {string} token 将Token添加到请求头Header中
     * @apiParam  {int} page
     * @apiParam  {int} limit
     * @apiName 44
     */
    public function order($page = '', $limit = '')
    {
        $user_id = Token::getCurrentTokenUserId();
        (new ValidPage())->goCheck();
        return ConsumptionOrder::getList($page-1,$limit,$user_id);
    }

    public function getRangeTotalAmount($user_id, $where)
    {
        return ConsumptionOrder::getRangeTotalAmount($user_id, $where);
    }
}
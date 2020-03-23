<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/31 21:50
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\api\service\Token;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ValidPage;
use think\Db;


class Message extends BaseController
{

    /**
     * @apiDefine  message 站内信
     */

    /**
     * @api {get} message/list  1、信息列表
     * @apiGroup message
     * @apiVersion 0.1.0
     * @apiDescription 信息列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/message/list?page=1&limit=10
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} page
     * @apiParam  {int} limit
     * @apiName 38
     * @apiSuccess {json} data 信息列表
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
    {
    "code": 0,
    "msg": "总共有2条数据",
    "count": 2,
    "data": [
    {
    "id": {
    "id": 2,// 信息id 进入详情页需要使用此字段
    "is_read": 1,// 是否已读，0未读，1已读
    "is_del": 0// 是否删除，0未删除，1删除 删除了就不显示此列表 --此项目暂无该需求，预留项
    },
    "title": "寿县人APP1.0版本正式上线啦" // 标题
    },
    {
    "id": {
    "id": 1,
    "is_read": 1,
    "is_del": 1
    },
    "title": "欢迎使用寿县人APP"
    }
    ],
    "requestUrl": "http://sxr.ijiandian.com/api/v1/message/list?page=1&limit=10"
    }
     */
    public function getMessageList($page = '', $limit = '')
    {
        (new ValidPage())->goCheck();
        return \app\api\model\Message::getMessageList($page-1, $limit);
    }

    /**
     * @api {get} message/read/id  2、详情
     * @apiGroup message
     * @apiVersion 0.1.0
     * @apiDescription 信息详情页，传id
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/message/read/id
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} id 信息ID
     * @apiName 39
     */
    public function getMessage($id='')
    {
        $user_id = Token::getCurrentTokenUserId();
        $result = \app\api\model\Message::getMessage($id,$user_id);
        if($result)
            throw new SuccessMessage(['data'=>$result]);
    }



}
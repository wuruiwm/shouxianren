<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/24
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\api\service\Token;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ValidPage;

class Action extends BaseController
{
    protected $beforeActionList = [
        'mustBeGet' => ['only' => 'readaction','readactionbyid'],
        'mustBePost' => ['only' => ''],
    ];

    /**
     * @api {get} action/read 2、全部活动列表
     * @apiGroup action
     * @apiVersion 0.1.0
     * @apiDescription  全部所有列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/action/read?page=1&limit=1
     * @apiParam  {int} page 当前页
     * @apiParam  {int} limit 每页取n条
     * @apiName 20
     * @apiSuccess {json} data 活动列表
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
    {
    "code": 0,
    "msg": "总共有6条数据",
    "count": 6,
    "data": [ // 多维数组
    {
    "id": 3, // 活动文章id
    "tid": {
    "id": 3,
    "is_awesome": "no" // 是否已点赞 yes/no
    },
    "title": "商务套房大床2米", // 活动标题
    "head_img": { // 封面图
    "count": 3,// 数量，你可以来控制显示样式，等于1时一大图显示，等于2时两图并列显示，大于等于3时三图排列显示（考虑优化）
    "data": [
    {
    "id": 64,
    "filepath": "http://sxr.ijiandian.com//uploads/admin/20190515/17879bfa08cd6b69621bee65abd1c19f.png" // 图片地址
    },
    {
    "id": 82,
    "filepath": "http://sxr.ijiandian.com//uploads/admin/20190524/d6764807c448e6ffe16844486eb30285.jpg"
    },
    {
    "id": 91,
    "filepath": "http://sxr.ijiandian.com//uploads/admin/20190524/0ab8d0653e85a1415ab62857a1a8176d.jpg"
    }
    ]
    },
    "news_type": 1, // 活动类型，0图文，1视频
    "video_id": { // 视频
    "id": 2,
    "url": "http://sxr.ijiandian.com//uploads/admin/video/sp1.mp4", // 视频连接
    "fileext": "video/mp4"
    },
    "action_cate": { // 栏目类型
    "id": 3,
    "title": "客房"
    },
    "read_num": 72,// 真实阅读量，页面显示== 真实阅读量+虚拟阅读量
    "virtual_read_num": 1111,// 虚拟阅读量
    "comment_num": 3,// 评论数
    "awesome_num": 0,// 真实点赞量 页面显示== 真实点赞量+虚拟点赞量
    "virtual_awesome_num": 800,// 虚拟点赞量
    "top": 2, // 是否置顶 1否 2是
    "merchant": { // 商户信息
    "id": 2,
    "title": "展业国际酒店", // 商户名称
    "img": {
    "id": 4,
    "url": "http://sxr.ijiandian.com/uploads/user/20190507/1f253b2edec2d0efd9c395be8cecc846.png" // 商户头像
    },
    "user": 3 // 用户id
    },
    "create_time": "05-15 09:27"
    }
    ],
    "requestUrl": "/api/v1/action/read?page=1&limit=1"
    }
     */


    /**
     * @api {get} action/read?page=1&limit=10&type=2  3、指定栏目列表
     * @apiGroup action
     * @apiVersion 0.1.0
     * @apiDescription  根据栏目类型 获取指定栏目列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/action/read?page=1&limit=10&type=2
     * @apiParam  {string} token header头部传参
     * @apiParam  {int} page 当前页
     * @apiParam  {int} limit 每页取n条
     * @apiParam  {int} [type] 活动栏目类型ID, 如果不传则取所有类型
     * @apiName 21
     * @apiSuccess {json} data 活动列表，以下是无数据返回示例，有数据示例请查看api接口 2、所有活动列表
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
    {
    "code": 0,
    "msg": "暂无相关数据",
    "count": 0,
    "data": null,
    "requestUrl": "/api/v1/action/read?page=1&limit=2&type=2"
    }
     */
    public function readAction($page='', $limit='', $type='')
    {
        (new ValidPage())->goCheck();
        return \app\api\model\Action::readAction($page-1,$limit,$type);
    }




    /**
     * @api {get} action/read/id?id=1  4、详情信息
     * @apiGroup action
     * @apiVersion 0.1.0
     * @apiDescription  根据活动文章id获取详情信息
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/news/read/id?id=1
     * @apiParam  {int} id 活动文章ID
     * @apiName 22
     */
    public function readActionById($id=''){
        // Token::getCurrentTokenUserId();
        $reslut = \app\api\model\Action::readActionById($id);
        if(!$reslut)
            throw new ErrorMessage();
        throw new SuccessMessage([
            'data'=>$reslut
        ]);
    }
}
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
use app\api\model\News as newsModel;
use app\api\service\Token;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ValidPage;


class News extends BaseController
{
    protected $beforeActionList = [
        'mustBeGet' => ['only' => 'readnews','readnewsbyid'],
        'mustBePost' => ['only' => 'test'],
    ];

    /**
     * @apiDefine  news 新闻
     */

    /**
     * @api {get} news/read 1、所有新闻列表
     * @apiGroup news
     * @apiVersion 0.1.0
     * @apiDescription  获取所有新闻列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/news/read?page=1&limit=10
     * @apiParam  {int} page 当前页
     * @apiParam  {int} limit 每页取n条
     * @apiName 3
     * @apiSuccess {json} data 新闻列表
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
    {
    "code": 0,
    "msg": "总共有11条数据",
    "count": 11,
    "data": [
    {
    "id": 2,
    "title": "当时父母念 今日尔应知",// 文章标题
    "head_img": [ // 文章封面图，当文章类型为视频模式时，只有一张图
    {
    "id": 55,
    "filepath": "http://sxr.ijiandian.com//uploads/admin/20190514/20fdb85d05c12d4e929a7fee7a4c420f.jpg"
    }
    ],
    "news_type": 1,// 文章类型，0 图文模式，1 视频模式
    "news_cate": { // 栏目类型
    "id": 1,
    "title": "头条"
    },
    "read_num": 0, // 阅读量，页面显示阅读量== read_num + virtual_read_num
    "virtual_read_num": 541,// 虚拟阅读量
    "comment_num": 0,// 评论数量
    "awesome_num": 0,// 点赞量，显示同理(阅读量)计算
    "virtual_awesome_num": 288,// 虚拟点赞
    "top": 2, //是否置顶，1未置顶，2置顶
    "create_time": "2019-05-14 11:14:35"
    }
    ],
    "requestUrl": "/api/v1/news/read?page=1&limit=1&type=1"
    }
     */

    /**
     * @api {get} news/read?page=1&limit=10&type=2 2、指定新闻栏目新闻列表
     * @apiGroup news
     * @apiVersion 0.1.0
     * @apiDescription  根据新闻类型 获取指定新闻栏目新闻列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/news/read?page=1&limit=10&type=2
     * @apiParam  {string} token header头部传参
     * @apiParam  {int} page 当前页
     * @apiParam  {int} limit 每页取n条
     * @apiParam  {int} [type] 新闻类型ID 不传取所有新闻
     * @apiName 4
     */
    public function readNews($page='', $limit='', $type='')
    {
        (new ValidPage())->goCheck();
        return newsModel::readNews($page-1,$limit,$type);
    }




    /**
     * @api {get} news/read/id?id=1  3、文章详情信息
     * @apiGroup news
     * @apiVersion 0.1.0
     * @apiDescription  根据新闻文章id获取详情信息
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/news/read/id?id=1
     * @apiParam  {int} id 文章ID
     * @apiName 5
     */
    public function readNewsById($id=''){
        // Token::getCurrentTokenUserId();
        $reslut = newsModel::readNewsById($id);
        if(!$reslut)
            throw new ErrorMessage();
        throw new SuccessMessage([
            'data'=>$reslut
        ]);
    }
  
  public function readIndependentNewsById($id=''){
    header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET,POST');
        $reslut = newsModel::readIndependentNewsById($id);
        if(!$reslut)
            throw new ErrorMessage();
        throw new SuccessMessage([
            'data'=>$reslut
        ]);
    }

    /**
     * @api {get} news/search  5、搜索
     * @apiGroup news
     * @apiVersion 0.1.0
     * @apiDescription  搜索新闻
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/news/search
     * @apiParam  {string} key 关键词
     * @apiParam  {int} page
     * @apiParam  {int} limit
     * @apiName 44
     */
    public function newSearch($page='', $limit='',$key=''){
        (new ValidPage())->goCheck();
        return newsModel::newSearch($page-1, $limit,$key);
    }
}
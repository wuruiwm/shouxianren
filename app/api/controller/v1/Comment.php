<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/16
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\api\model\News;
use app\api\model\User;
use app\api\service\Token;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ValidComment;
use app\api\model\Comment as CommentModel;


class Comment extends BaseController
{

    protected $beforeActionList = [
        'mustBeGet' => ['only' => '',''],
        'mustBePost' => ['only' => 'createcomment'],
    ];

    /**
     * @apiDefine  comment 评论
     */

    /**
     * @api {post} comment/create 1、发表评论
     * @apiGroup comment
     * @apiVersion 0.1.0
     * @apiDescription  发表评论
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/comment/create
     * @apiParam  {string} token header头部传参
     * @apiParam  {string} content 评论内容
     * @apiParam  {int} type 评论类型：1新闻，2活动，3广场
     * @apiParam  {int} article 评论文章ID
     * @apiName 10
     */

    public function createComment(){
        $user_id = Token::getCurrentTokenUserId();
        (new ValidComment())->goCheck();
        $post = input('post.');
        $post['user'] = $user_id;
        $post['content'] = strip_tags($post['content']);
        $result = CommentModel::createComment($post); // 评论信息
        $result['time'] = timeToStr(time(),'m-d H:i');
        unset($result['article']);
        unset($result['user']);
        unset($result['update_time']);
        unset($result['create_time']);
        unset($result['id']);
        $result_user = User::exist('id',$user_id,['avatar','nickname'],false);
        throw new SuccessMessage([
            'data'=>[
                'user'=>$result_user,
                'content'=>$result
            ]
        ]);

    }
}
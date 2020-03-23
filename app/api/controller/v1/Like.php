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

use app\api\model\Like as LikeModel;
use app\api\model\User;
use app\api\service\Token;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ValidLike;


class Like extends BaseController
{

    /**
     * @apiDefine  like 点赞
     */

    /**
     * @api {post} like/okorno 1、点赞/取消点赞
     * @apiGroup like
     * @apiVersion 0.1.0
     * @apiDescription  点赞/取消点赞
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/like/okorno
     * @apiParam  {string} token header头部传参
     * @apiParam  {int} type 点赞类型：1新闻，2活动，3广场
     * @apiParam  {int} article 点赞ID
     * @apiName 18
     */

    public function createLike()
    {
        $user_id = Token::getCurrentTokenUserId();

        (new ValidLike())->goCheck();
        $post = input('post.');
        $post['user'] = $user_id;
        $result = LikeModel::where('user', $user_id)
            ->where('type', $post['type'])
            ->where('article', $post['article'])
            ->find();

        if ($result) {
            $result = $this->deleteLike($post);
            if ($result)
                throw new SuccessMessage([
                    'msg' => '取消点赞',
                    'data'=>User::where('id',$user_id)->field(['id','avatar','nickname'],false)->find()
                ]);
        }

        $result = LikeModel::createLike($post);
        if ($result)
            throw new SuccessMessage([
                'msg' => '成功点赞',
                'data'=>User::where('id',$user_id)->field(['id','avatar','nickname'],false)->find()
            ]);


    }

    public function deleteLike($post)
    {
        return LikeModel::deleteLike($post);
    }
}
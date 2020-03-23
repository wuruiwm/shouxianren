<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/6/1
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\api\service\Token;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ValidPage;

class Square extends BaseController
{
    /**
     * @apiDefine  square 朋友圈(广场)
     */

    /**
     * @api {post} square/publish  1、发表动态
     * @apiGroup square
     * @apiVersion 0.1.0
     * @apiDescription  支持图片、内容二选一 或 同生
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/square/publish
     * @apiParam  {string} token header头部传参
     * @apiParam  {string} [img] 图片id, 多张以,逗号分割
     * @apiParam  {string} [content] 内容
     * @apiName 41
     */

    public function publish()
    {
        $user_id = Token::getCurrentTokenUserId();
        $post = input('post.');
        if (empty($post['img'])) {
            if (empty(trim($post['content']))) {
                throw new ErrorMessage(['msg' => '图片或内容不能为空']);
            }
        }
        $post['user'] = $user_id;
        $post['ip'] = $this->request->ip();//IP
        $result = \app\api\model\Square::publish($post);
        if ($result)
            throw new SuccessMessage(['msg' => '发布成功']);
        throw new ErrorMessage(['msg' => '发布失败']);
    }

    /**
     * @api {post} square/read  2、列表信息
     * @apiGroup square
     * @apiVersion 0.1.0
     * @apiDescription  朋友圈列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/square/read
     * @apiParam  {string} token header头部传参
     * @apiParam  {int} page
     * @apiParam  {int} limit
     * @apiName 42
     */
    public function readList($page='', $limit='')
    {
        (new ValidPage())->goCheck();
        return   \app\api\model\Square::readList($page-1,$limit);
    }
}
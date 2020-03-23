<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/6/24
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\model\Action;
use app\admin\model\Message;
use app\admin\model\News;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\push\JpushSend;
use think\Controller;

use app\lib\push\JPushs;


class JPusha extends Controller
{
    public function jpush()
    {
        $postData = input('post.');
        $push_data = [
            'title' => $postData['title'],
            'content' => $postData['content']
        ];
        $push_param = [
            'path' => $postData['path'],
            'id' => $postData['id']
        ];
        $test = new JpushSend();
        $res = $test->send_pub($postData['alias'], $push_data, $push_param);
        if ($res == 'success') {
            throw new SuccessMessage();
        }
        throw new ErrorMessage();
    }

    public function updateJpushStatus()
    {
        $get = input('get.');
        if ($get['path'] == 'news') {
            $new = new News();
            return $new->update(['is_jpush' => 1], ['id' => $get['id']]);
        }
        if ($get['path'] == 'action') {
            $action = new Action();
            return $action->update(['is_jpush' => 1], ['id' => $get['id']]);
        }

        if ($get['path'] == 'notice') {
            $message = new Message();
            return $message->update(['is_jpush' => 1], ['id' => $get['id']]);
        }

    }

}
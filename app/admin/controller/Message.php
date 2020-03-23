<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/31 21:49
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;

class Message extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }


    public function publish()
    {
        return $this->fetch();
    }

    public function update()
    {
        return $this->fetch();
    }
    public function createMessage()
    {
        $result =  \app\admin\model\Message::createMessage(input('post.'));
        if($result)
            throw new SuccessMessage();
        throw new ErrorMessage();

    }
    public function updateMessage($id){
        return \app\admin\model\Message::updateMessage($id,input('post.'));
    }

    public function readMessage($page = '', $limit = '')
    {
        return \app\admin\model\Message::readMessage($page - 1, $limit);
    }

    public function readMessageById($id='')
    {
        $result = \app\admin\model\Message::readMessageById($id);
        if($result)
            throw new SuccessMessage(['data'=>$result]);
        throw new ErrorMessage();
    }
    public function deleteMessage($id){
        return \app\admin\model\Message::deleteMessage($id);
    }
}
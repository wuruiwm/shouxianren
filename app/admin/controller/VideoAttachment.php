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

namespace app\admin\controller;


use app\lib\exception\SuccessMessage;

class VideoAttachment extends Permissions
{
    public function createVideo($data=[]){
        $result = \app\admin\model\VideoAttachment::createVideo($data);
        $id = $result->getLastInsID();
        throw new SuccessMessage([
            'data'=>['id'=>$id]
        ]);
    }
}
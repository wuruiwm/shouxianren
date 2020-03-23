<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/11
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;
use \app\admin\model\Qiniuconfig as QiniuModel;
use app\lib\exception\SuccessMessage;
use app\lib\validate\Qiniu;

class Qiniuconfig extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }


    public function updateQiniuconfig(){
        $type = input('post.type');
        if($type==1)
            (new Qiniu())->goCheck();
        return QiniuModel::updateQiniuconfig(input('post.'),$id=1);
    }

    public function readQiniuconfig()
    {
        $result =  QiniuModel::readQiniuconfig($id=1);
        throw new SuccessMessage([
            'data'=>$result
        ]);
    }
}
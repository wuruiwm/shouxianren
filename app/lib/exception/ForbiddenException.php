<?php
// +----------------------------------------------------------------------
// | api [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/3/21
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $statusCode = 403;
    public $msg = '未经授权:访问由于凭据无效被拒绝!';
    public $code = 40301;
}
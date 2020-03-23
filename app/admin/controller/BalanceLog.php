<?php
// +----------------------------------------------------------------------
// | shouxianren_app [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/21
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\service\PHPExcel;
use app\lib\validate\ValidPage;

class BalanceLog extends Permissions
{
    public function index(){
        return $this->fetch();
    }


    public function getList($page = '', $limit = '', $key = '')
    {
        (new ValidPage())->goCheck();
        return \app\admin\model\BalanceLog::getList($page - 1, $limit, $key);
    }

    public function dataExport($type='',$time='',$order)
    {
        $firmExcel = new PHPExcel();
        $filename = '余额记录';
        $title = ['订单号','用户','操作行为','符号','数目','来源类型','备注说明','操作时间'];
        $field = ['order','user_copy','use_action','type','amount','source_type','remark','create_time'];
        $database = \app\admin\model\BalanceLog::getExportList($type,$time,$order);
        $firmExcel->export($filename, $title, $field, $database);
    }

}
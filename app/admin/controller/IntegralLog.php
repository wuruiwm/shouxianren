<?php
// +----------------------------------------------------------------------
// | shouxianren_app [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/20
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\service\PHPExcel;
use app\lib\validate\ValidPage;

class IntegralLog extends Permissions
{
    public function index(){
        return $this->fetch();
    }

    public function getList($page = '', $limit = '', $key = '')
    {
        (new ValidPage())->goCheck();
        return \app\admin\model\IntegralLog::getList($page - 1, $limit, $key);
    }

    public function dataExport($type='',$time='')
    {

        $firmExcel = new PHPExcel();
        $filename = '积分记录';
        $title = ['用户','操作行为','运算符','数目','来源类型','备注说明','操作时间'];
        $field = ['user_copy','use_action_copy','type','amount','source_type_copy','remark','create_time'];
        $database = \app\admin\model\IntegralLog::getExportList($type,$time);
        $firmExcel->export($filename, $title, $field, $database);
    }
}
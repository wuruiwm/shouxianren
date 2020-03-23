<?php
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/30
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\model\Merchant;
use app\admin\service\PHPExcel;
use app\lib\validate\ValidPage;

class ConsumptionOrder extends Permissions
{
    public function index(){
        $merchant = Merchant::all();
        $this->assign('merchant', $merchant);
        return $this->fetch();
    }

    public function getList($page = '', $limit = '', $key = '')
    {
        (new ValidPage())->goCheck();
        return \app\admin\model\ConsumptionOrder::getList($page - 1, $limit, $key);
    }

    // 导出消费记录数据
    public function dataExport()
    {
        $key = '';
        $ids = input('get.ids');
        if(!empty($ids)){
            $ids = explode(',', $ids);
        }else{
            $ids = [];
        }
        $firmExcel = new PHPExcel();
        $filename = '扫码付款消费记录';
//        $title = ['id', '订单号','商家','用户','使用券','消费积分','积分抵扣','消费余额','总金额','支付时间'];
//        $field = ['id', 'order','merchant_copy','user_copy','coupon_copy','integral','merchant_integral_deduction_copy','balance','total_amount','create_time'];



        $title = ['id', '订单号','商家','用户','优惠券名称','优惠券面值','积分抵扣','消费余额','实付金额','支付方式','总金额','返积分','积分返比%','支付时间'];
        $field = ['id', 'order','merchant_copy','user_copy','coupon_copy','coupon_copy1','merchant_integral_deduction_copy','balance','real_payment','pay_type','total_amount','integral_rebate','integral_ratio','create_time'];

        $database = \app\admin\model\ConsumptionOrder::getExportList($ids);
        $firmExcel->export($filename, $title, $field, $database,$type='Excel2007',$sum=1);
    }
  
   public function dataExportAide($merchant='',$mobile='',$order='',$time=''){
        $firmExcel = new PHPExcel();
        $filename = '扫码付款消费记录';
       $title = ['id', '订单号','商家','用户','优惠券名称','优惠券面值','积分抵扣','消费余额','实付金额','支付方式','总金额','返积分','积分返比%','支付时间'];
       $field = ['id', 'order','merchant_copy','user_copy','coupon_copy','coupon_copy1','merchant_integral_deduction_copy','balance','real_payment','pay_type','total_amount','integral_rebate','integral_ratio','create_time'];

       $database = \app\admin\model\ConsumptionOrder::getExportListAide($merchant,$mobile,$order,$time);
        $firmExcel->export($filename, $title, $field, $database,$type='Excel2007',$sum=1);
    }
}
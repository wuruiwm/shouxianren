<?php
/*
 * @Author: 傍晚升起的太阳
 * @QQ: 1250201168
 * @Email: wuruiwm@qq.com
 * @Date: 2019-11-22 16:35:41
 * @LastEditors: 傍晚升起的太阳
 * @LastEditTime: 2019-11-27 15:44:21
 */
// +----------------------------------------------------------------------
// | sxr.ijiandian.com [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/26
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\controller;


use app\admin\model\Merchant;
use app\admin\model\User;
use app\admin\service\PHPExcel;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\CreateCoupon;
use app\lib\validate\ValidPage;
use think\Db;

class Coupon extends Permissions
{
    public function index()
    {
        $merchant = Merchant::all();
        $this->assign('merchant', $merchant);
        return $this->fetch();
    }


    public function publish()
    {
        $merchant = Merchant::all();
        $this->assign('merchant', $merchant);
        $user = User::where('status', '=', 1)->select();
        $this->assign('user', $user);

        return $this->fetch();
    }

    public function update()
    {
        $merchant = Merchant::all();
        $this->assign('merchant', $merchant);
        $user = User::where('status', '=', 1)->select();
        $this->assign('user', $user);
        return $this->fetch();
    }

    public function createCoupon()
    {
        (new CreateCoupon())->goCheck();
        $post = input('post.');

        // 优惠券使用时间限制
        if ($post['limit_time_type'] == 1) {
            if (empty($post['day']) || !preg_match('/^\d*$/', $post['day']))
                throw new ErrorMessage(['msg' => '有效天数格式错误']);
        }
        if ($post['limit_time_type'] == 2) {
            if (empty($post['time']))
                throw new ErrorMessage(['msg' => '请选择时间范围']);
            $time = explode(" - ", $post['time']);
            $post['start_time'] = GetMkTime($time[0]);
            $post['end_time'] = GetMkTime($time[1]);
        }
        // 领取条件
        if ($post['type'] == 2) {
            if (empty($post['integral']) || !preg_match('/^\d*$/', $post['integral']))
                throw new ErrorMessage(['msg' => '所需积分输写格式错误']);
        }
        if ($post['type'] == 3) {
            if (empty($post['balance']) || !preg_match('/^\d*$/', $post['balance']))
                throw new ErrorMessage(['msg' => '所需余额输写格式错误']);
        }
        if ($post['type'] == 4) {
            if (empty($post['integral']) || !preg_match('/^\d*$/', $post['integral']))
                throw new ErrorMessage(['msg' => '所需积分输写格式错误']);

            if (empty($post['balance']) || !preg_match('/^\d*$/', $post['balance']))
                throw new ErrorMessage(['msg' => '所需余额输写格式错误']);
        }
        $result = \app\admin\model\Coupon::createOrUpdateCoupon($post);

        if ($result)
            throw new SuccessMessage();
        throw new ErrorMessage(['msg' => '操作失败，请稍后重试']);
    }


    public function getList($page = '', $limit = '', $key = '')
    {
        (new ValidPage())->goCheck();
        return \app\admin\model\Coupon::getList($page - 1, $limit, $key);
    }

    public function getListById($id = '')
    {
        $result = \app\admin\model\Coupon::getInfoById($id);
        if ($result) {
            throw new SuccessMessage(['data' => $result]);
        }
        throw new ErrorMessage();
    }


    public function updateById($id = '')
    {
        $result = \app\admin\model\Coupon::updateById($id, input('post.'));
        if ($result)
            throw new SuccessMessage();
        throw new ErrorMessage();
    }

    public function dataExport()
    {
        $key = '';
        $firmExcel = new PHPExcel();
        $filename = '营销券列表';
        $title = ['id', '发布商家','优惠券名称','优惠券面值','有效期','优惠券总量','被领取','已使用','剩余券','领取条件','使用条件','创建时间'];
        $field = ['id', 'merchant_name','title','face_value','valid_time','number','receive','use_number','surplus','receive_type','amount','create_time'];
        $database = \app\admin\model\Coupon::getExportList();
        foreach($database as $m=>$n){
            $merchant = json_decode($n['merchant'],true);
            $database[$m]['merchant_name'] = $merchant['title'];
            if($m['limit_time_type']==1){
                $database[$m]['valid_time'] = $n['day']['str'];
            }else{
                $database[$m]['valid_time'] = $n['start_time'].'至'.$n['end_time'];
            }
            if($n['type']==1){
                $database[$m]['receive_type'] = '直接领取';
            }else if($n['type']==2){
                $database[$m]['receive_type'] = $n['integral'].'积分兑换';
            }else if($n['type']==3){
                $database[$m]['receive_type'] = $n['balance'].'余额兑换';
            }else if($n['type']==4) {
                $database[$m]['receive_type'] = $n['integral'] . '积分兑换' . ' 或 ' . $n['balance'] . '余额兑换';
            }
            if($n['use_condition']['amount']=='0.00'){
                $database[$m]['amount'] = '无条件';

            }else{
                $database[$m]['amount'] = '消费满'.$n['use_condition']['amount'].'元可用';
            }
        }
//        return json($database);
        $firmExcel->export($filename, $title, $field, $database);
    }
    public function bag_index(){
        return $this->fetch();
    }
    public function bag_list(){
        extract(page());
        $data = Db::name('coupon_bag')->where('title','like','%'.input('title').'%')->limit($number,$limit)->select();
        $data = array_date($data,['create_time','update_time']);
        $count = Db::name('coupon_bag')->where('title','like','%'.input('title').'%')->count();
        showjson(['data'=>$data,'code'=>0,'count'=>$count]);
    }
    public function bag_post(){
        $title = input('title');
        $price = input('price');
        //$coupon = $_POST['coupon'];
        if(!isset($_POST['coupon']) || !is_array($_POST['coupon'])){
            msg(0,'请选择优惠券');
        }
        if(empty($title)){
            msg(0,'请输入标题');
        }
        if(empty($price) || !is_numeric($price)){
            msg(0,'请输入标题');
        }
        $coupon = $_POST['coupon'];
        $price = round($price,2);
        $coupon = array_unique($coupon);
        $coupon_str = '';
        foreach ($coupon as $k => $v) {
            if(!empty($v)){
                $coupon_str .= $v.',';
            }
        }
        if(empty($coupon_str)){
            msg(0,'请选择优惠券');
        }
        $coupon_str = rtrim($coupon_str,',');
        $data = [
            'title'=>$title,
            'price'=>$price,
            'coupon'=>$coupon_str,
            'update_time'=>time()
        ];
        //print_r($coupon);exit();
        $id = input('id');
        if (!empty($id) && is_numeric($id)) {
            $res = Db::name('coupon_bag')->where('id',$id)->update($data);
            if ($res == 1) {
                return ['status'=>1,'msg'=>'修改成功'];
            }else{
                return ['status'=>0,'msg'=>'修改失败'];
            }
        }else{
            $data['create_time'] = time();
            $res = Db::name('coupon_bag')->insert($data);
            if ($res == 1) {
                return ['status'=>1,'msg'=>'添加成功'];
            }else{
                return ['status'=>0,'msg'=>'添加失败'];
            }
        } 
    }
    public function bag_del(){
        $id = input('id');
        if (!empty($id) && is_numeric($id)) {
            $res = Db::name('coupon_bag')->where('id',$id)->delete();
            if ($res == 1) {
                return ['status'=>1,'msg'=>'删除成功'];
            }else{
                return ['status'=>0,'msg'=>'删除失败'];
            }
        }else{
            return ['status'=>0,'msg'=>'id不合法'];
        }
    }
    public function bag_add(){
        $data = Db::name('coupon')->where('type',5)->select();
        //\var_dump($data);exit();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function bag_edit(){
        $data = Db::name('coupon')->where('type',5)->select();
        $id = input('id');
        $id = intval($id);
        $coupon = Db::name('coupon_bag')->where('id',$id)->find();
        $coupon_list = Db::name('coupon')->field('id')->where('id','in',$coupon['coupon'])->select();
        $coupon['list'] = $coupon_list;
        $this->assign('coupon',$coupon);
        $this->assign('data',$data);
        return $this->fetch();
    }
}
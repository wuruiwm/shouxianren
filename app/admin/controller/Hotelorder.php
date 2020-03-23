<?php
namespace app\admin\controller;

use think\Db;

class Hotelorder extends Permissions{
    public function index(){
        $data = Db::name('action')->field(['id',"title"])->where('action_cate','3')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function list(){
        $page = input('page');
        $limit = input('limit');
        if (empty($page) || !is_numeric($page)) {
          return ['status'=>0,'msg'=>'请输入正确的页码'];
        }
        if (empty($limit) || !is_numeric($limit)) {
          return ['status'=>0,'msg'=>'请输入正确的条数'];
        }
        $number = ($page - 1) * $limit;
        $where = '';
        if(input('actionid')){
          if (!is_numeric(input('actionid'))) {
             return ['status'=>0,'msg'=>'请输入正确的酒店ID'];
          }
          $where = $where . 'actionid='.input('actionid')." ";
        }
        if (input('name_mobile')) {
            if (!empty($where)) {
              $where = $where . "and (mobile like '%".input('name_mobile')."%' or name like '%".input('name_mobile')."%')";
            }else{
              $where = $where . "mobile like '%".input('name_mobile')."%' or name like '%".input('name_mobile')."%'";
            }
        }
        if (!empty($where)) {
            $arr = Db::name('hotel_order')->where($where)->order('id desc')->limit($number,$limit)->select();
        }else{
            $arr = Db::name('hotel_order')->order('id desc')->limit($number,$limit)->select();
        }
        foreach ($arr as $k => $v) {
            if (!empty($v['orderid'])) {
              $pay_status = Db::name('consumption_order')->field(['status'])->where('order',$v['orderid'])->find()['status'];
              if (!empty($pay_status)) {
                $arr[$k]['is_pay'] = 1;
              }else{
                $arr[$k]['is_pay'] = 0;
              }
            }else{
              $arr[$k]['is_pay'] = 0;
            }
            $user_res = Db::name('user')->field(['nickname','mobile'])->where('id',$v['userid'])->find();
            if (!empty($user_res)) {
              $arr[$k]['username'] = $user_res['nickname'].':'.$user_res['mobile'];
            }else{
              $arr[$k]['username'] = '用户未找到';
            }
            $arr[$k]['start_time'] = date('Y-m-d',$v['start_time']);
            $arr[$k]['end_time'] = date('Y-m-d',$v['end_time']);
            $arr[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
        }
        $data = [];
        if (!empty($where)) {
          $count = Db::query("select count(id) from ijiandian_hotel_order where ".$where);
        }else{
          $count = Db::query("select count(id) from ijiandian_hotel_order");
        }
        
        $count = $count[0]['count(id)'];
        $data['count'] = $count;
        $data['code'] = 0;
        $data['data'] = $arr;
        return $data;
    }
    public function confirm(){
      $id = input('id');
      if (empty($id) || !is_numeric($id)) {
        return ['status'=>0,'msg'=>'请输入正确的数量'];
      }
      $hotel_order_res = Db::name('hotel_order')->where('id',$id)->find();
      if ($hotel_order_res['status'] == 0) {
        return ['status'=>0,'msg'=>'订单已经是取消状态,无法改变为已入住'];
      }
      if ($hotel_order_res['status'] == 2) {
        return ['status'=>0,'msg'=>'订单已经是完成状态,请勿重复确认'];
      }
      if ($hotel_order_res['status'] == 1) {
          $pay_status = Db::name('consumption_order')->field(['status'])->where('order',$hotel_order_res['orderid'])->find()['status'];
          if (!empty($pay_status)) {
              $update_res =  Db::name('hotel_order')->where('id',$id)->update(['status'=>2]);
                if (!empty($update_res)) {
                    return ['status'=>1,'msg'=>'确认订单入住成功'];
                }else{
                    return ['status'=>0,'msg'=>'确认订单入住失败'];
                }
          }else{
              return ['status'=>0,'msg'=>'订单未支付，不能确认入住'];
          }
      }
    }
    public function cancel(){
      $id = input('id');
      if (empty($id) || !is_numeric($id)) {
        return ['status'=>0,'msg'=>'请输入正确的数量'];
      }
      $hotel_order_res = Db::name('hotel_order')->where('id',$id)->find();
      if ($hotel_order_res['status'] == 0) {
        return ['status'=>0,'msg'=>'订单已经是取消状态,请勿重复取消'];
      }
      if ($hotel_order_res['status'] == 2) {
        return ['status'=>0,'msg'=>'订单已经是已入住状态,无法取消'];
      }
      if ($hotel_order_res['status'] == 1) {
          if (!empty($hotel_order_res['orderid'])) {
                $pay_status = Db::name('consumption_order')->field(['status'])->where('order',$hotel_order_res['orderid'])->find()['status'];
            if (!empty($pay_status)) {
                return ['status'=>0,'msg'=>'订单已支付，无法取消'];
            }
          }
        $update_res =  Db::name('hotel_order')->where('id',$id)->update(['status'=>0]);
        if (!empty($update_res)) {
          return ['status'=>1,'msg'=>'订单取消成功'];
        }else{
          return ['status'=>0,'msg'=>'订单取消失败'];
        }
      }
    }
}
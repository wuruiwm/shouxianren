<?php
namespace app\api\controller\v1;

use think\Db;
use app\api\service\Token;
class Hotelorder extends BaseController
{
    public function add(){
    	$user_id = Token::getCurrentTokenUserId();
    	$num = input('num');
    	$name = input('name');
    	$mobile = input('mobile');
    	$start_time = input('start_time');
    	$end_time = input('end_time');
    	$hotelid = input('hotelid');
    	if (empty($num) || !is_numeric($num)) {
    		return ['status'=>0,'msg'=>'请输入正确的数量'];
    	}
    	if (empty($name)) {
    		return ['status'=>0,'msg'=>'姓名不能为空'];
    	}
    	if (empty($mobile) || !preg_match("/^1[3456789]\d{9}$/", $mobile)) {
    		return ['status'=>0,'msg'=>'请输入正确的手机号'];
    	}
    	if (!strtotime($start_time)) {
    		return ['status'=>0,'msg'=>'请选择正确的入住时间'];
    	}
    	if (!strtotime($end_time)) {
    		return ['status'=>0,'msg'=>'请选择正确的退房时间'];
    	}
    	if (strtotime($end_time)<=strtotime($start_time)) {
    		return ['status'=>0,'msg'=>'退房时间要大于入住时间'];
    	}
    	if (empty($hotelid) || !is_numeric($hotelid)) {
    		return ['status'=>0,'msg'=>'请输入正确的房间ID'];
    	}
    	$hotel_res = Db::name('hotel')->field(['actionid','head_img','num','price','name','ban_time'])->where('id',$hotelid)->find();
    	if (empty($hotel_res)) {
    		return ['status'=>0,'msg'=>'房间已被删除,请返回重新选择房间'];
    	}
    	if ($hotel_res['num']<=0) {
    		return ['status'=>0,'msg'=>'房间剩余数量为0,请返回重新选择房间'];
    	}
    	if ($hotel_res['num']<$num) {
    		return ['status'=>0,'msg'=>'房间剩余数量不足,请返回重新选择数量'];
    	}
        if(strtotime($end_time) >= $hotel_res['ban_time']){
            return ['status'=>0,'msg'=>'只能预订'.date('Y-m-d',$hotel_res['ban_time']).'之前的房间'];
        }
    	$hotel_name = $hotel_res['name'];
    	$days = (strtotime($end_time)-strtotime($start_time))/86400;
    	$mid = Db::name('action')->field(['merchant'])->where('id',$hotel_res['actionid'])->find()['merchant'];
    	$merchant = Db::name('merchant')->field(['address','title'])->where('id',$mid)->find();
    	$address = $merchant['address'];
    	$merchant_name = $merchant['title'];
    	$data = [
    		'status'=>1,//0订单取消  1订单已提交，如果订单表的状态为已支付，状态则为已支付   2已入住
    		'userid'=>$user_id,
    		'orderid'=>0,
    		'actionid'=>$hotel_res['actionid'],
    		'num'=>$num,
    		'name'=>$name,
    		'mobile'=>$mobile,
    		'start_time'=>strtotime($start_time),
    		'end_time'=>strtotime($end_time),
    		'hotel_name'=>$hotel_name,
    		'mid'=>$mid,
    		'merchant_name'=>$merchant_name,
    		'address'=>$address,
    		'days'=>$days,
    		'head_img'=>$hotel_res['head_img'],
    		'price'=>$hotel_res['price']*$days*$num,
    		'hotelid'=>$hotelid,
    		'create_time'=>time(),
    		'update_time'=>time(),
    	];
    	Db::startTrans();
    	$hotel_order_insert = Db::name('hotel_order')->insert($data);
    	if (empty($hotel_order_insert)) {
    		Db::rollback();
    		return ['status'=>0,'msg'=>'提交失败'];
    	}
    	$orderid = Db::name('hotel_order')->getLastInsID();
    	$hotel_update = Db::name('hotel')->where('id',$hotelid)->setDec('num',$num);
    	if (empty($hotel_update)) {
    		Db::rollback();
    		return ['status'=>0,'msg'=>'提交失败'];
    	}
    	Db::commit();
    	return ['status'=>1,'msg'=>'提交成功','id'=>$orderid];
    }
    public function list(){
    	$user_id = Token::getCurrentTokenUserId();
    	$hotel_order_res = Db::name('hotel_order')->field(['id','hotel_name','merchant_name','start_time','end_time','days','num','status','address','price','orderid'])->order('id desc')->where('userid',$user_id)->select();
    	if (empty($hotel_order_res)) {
    		return ['status'=>0,'data'=>[]];
    	}
    	foreach ($hotel_order_res as $k => $v) {
    		if (!empty($v['orderid'])) {
    			$pay_status = Db::name('consumption_order')->field(['status'])->where('order',$v['orderid'])->find()['status'];
    			if (!empty($pay_status)) {
    				$hotel_order_res[$k]['is_pay'] = 1;
    			}else{
    				$hotel_order_res[$k]['is_pay'] = 0;
    			}
    		}else{
    			$hotel_order_res[$k]['is_pay'] = 0;
    		}
    		$hotel_order_res[$k]['start_time'] = date('Y-m-d',$v['start_time']);
    		$hotel_order_res[$k]['end_time'] = date('Y-m-d',$v['end_time']);
    	}
    	return ['status'=>1,'data'=>$hotel_order_res];
    }
    public function detail(){
    	$user_id = Token::getCurrentTokenUserId();
    	$id = input('id');
    	if (empty($id) || !is_numeric($id)) {
    		return ['status'=>0,'msg'=>'请输入正确的ID'];
    	}
    	$hotel_order_res = Db::name('hotel_order')->field(['id','hotel_name','merchant_name','start_time','end_time','create_time','days','num','status','price','name','mobile','mid'])->where('userid',$user_id)->where('id',$id)->find();
    	$hotel_order_res['start_time'] = date('Y年m月d日',$hotel_order_res['start_time']);
		$hotel_order_res['end_time'] = date('Y年m月d日',$hotel_order_res['end_time']);
		$hotel_order_res['create_time'] = date('Y/m/d H:i:s',$hotel_order_res['create_time']);
    	if (empty($hotel_order_res)) {
    		return ['status'=>0,'msg'=>'订单不存在'];
    	}
    	if ($hotel_order_res['status'] == 0) {
    		return ['status'=>0,'msg'=>'订单已取消'];
    	}
    	if ($hotel_order_res['status'] == 2) {
    		return ['status'=>0,'msg'=>'订单已入住'];
    	}
    	$user_res = Db::name('user')->field(['integral','balance'])->where('id',$user_id)->find();
    	$hotel_order_res['integral'] = $user_res['integral'];
    	$hotel_order_res['balance'] = $user_res['balance'];
    	$data = ['status'=>1,'data'=>$hotel_order_res];
    	$hotel_orderid = Db::name('hotel_order')->field(['orderid'])->where('id',$id)->find()['orderid'];
    	if (!empty($hotel_orderid)) {
    		$pay_status = Db::name('consumption_order')->field(['status'])->where('order',$hotel_orderid)->find()['status'];
    		if (!empty($pay_status)) {
    			return ['status'=>0,'msg'=>'订单已支付，无法再次付款'];
    		}
    	}
    	return $data;
    }
    public function bindorder(){
    	$user_id = Token::getCurrentTokenUserId();
    	$hotel_order_id = input('id');
    	$orderid = input('ordersn');
    	if (empty($hotel_order_id) || !is_numeric($hotel_order_id)) {
    		return ['status'=>0,'msg'=>'请输入正确的ID'];
    	}
    	if (empty($orderid) || !is_numeric($orderid)) {
    		return ['status'=>0,'msg'=>'请输入正确的订单ID'];
    	}
    	$order_res = Db::name('consumption_order')->where('order',$orderid)->find();
    	if (empty($order_res)) {
    		return ['status'=>0,'msg'=>'请输入正确的订单ID'];
    	}
    	$hotel_orderid = Db::name('hotel_order')->field(['orderid'])->where('id',$hotel_order_id)->find()['orderid'];
    	if (!empty($hotel_orderid)) {
    		$pay_status = Db::name('consumption_order')->field(['status'])->where('order',$hotel_orderid)->find()['status'];
    		if (!empty($pay_status)) {
    			return ['status'=>0,'msg'=>'订单已支付，请勿重复支付'];
    		}
    	}
    	$update_res = Db::name('hotel_order')->where('id',$hotel_order_id)->update(['orderid'=>$orderid]);
    	if (!empty($update_res)) {
    		return ['status'=>1];
    	}else{
    		$myfile = fopen('./bindorder.log', 'a');
    		$log = date('Y-m-d H:i:s')."\n";
    		$log = $log . '用户ID:'.$user_id."\n";
    		$log = $log . 'GET:' . json_encode($_GET)."\n";
    		$log = $log . 'POST:' . json_encode($_POST)."\n\n";
			fwrite($myfile, $log);
			fclose($myfile);
			return ['status'=>0,'msg'=>'提交订单失败,请联系管理员查看日志'];
    	}
    }
    public function cancel(){
    	$user_id = Token::getCurrentTokenUserId();
    	$id = input('id');
    	if (empty($id) || !is_numeric($id)) {
    		return ['status'=>0,'msg'=>'请输入正确的ID'];
    	}
    	$hotel_orderid = Db::name('hotel_order')->field(['orderid'])->where('id',$id)->find()['orderid'];
    	if (!empty($hotel_orderid)) {
    		$pay_status = Db::name('consumption_order')->field(['status'])->where('order',$hotel_orderid)->find()['status'];
    		if (!empty($pay_status)) {
    			return ['status'=>0,'msg'=>'订单已支付，无法取消订单'];
    		}
    	}
    	$update_res = Db::name('hotel_order')->where('id',$id)->where('userid',$user_id)->update(['status'=>0]);
    	if (!empty($update_res)) {
    		$hotel_order_res = Db::name('hotel_order')->field(['hotelid','num'])->where('id',$id)->find();
    		Db::name('hotel')->where('id',$hotel_order_res['hotelid'])->setInc('num',$hotel_order_res['num']);
    		return ['status'=>1,'msg'=>'取消订单成功'];
    	}else{
			return ['status'=>0,'msg'=>'请勿重复取消订单'];
    	}
    }
}
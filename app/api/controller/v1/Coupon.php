<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/26
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


use app\api\model\BalanceLog;
use app\api\model\CouponLog;
use app\api\model\IntegralLog;
use app\api\model\User;
use app\api\service\Token;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ReceiveCoupon;
use think\Exception;
use think\Db;
class Coupon extends BaseController
{
    protected $beforeActionList = [
        'mustBeGet' => ['only' => 'readcoupon'],
        'mustBePost' => ['only' => 'receivecoupon'],
    ];

    /**
     * @apiDefine  coupon 优惠券
     */

    /**
     * @api {get} coupon/read?action=1  1、优惠券列表
     * @apiGroup coupon
     * @apiVersion 0.1.0
     * @apiDescription  首页礼包直接领取券列表，个人中心 积分/余额 兑换券列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/coupon/read?action=1
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} action 1首页礼包优惠券列表，2个人中心兑换券
     * @apiName 27
     * @apiSuccess {json} data 优惠券
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     * {
     * "code": 0,
     * "msg": "总共有1条数据",
     * "count": 1,
     * "data": [
     * {
     * "id": 6,
     * "merchant": { // 商家信息
     * "id": 1,
     * "title": "寿州国际酒店",
     * "img": {
     * "id": 70,
     * "url": "http://sxr.ijiandian.com/uploads/admin/20190518/329d53972632fccc1f185c74ce185301.jpg"
     * },
     * "user": 1
     * },
     * "limit_user": "1",
     * "title": "给17681125543发优惠券", // 优惠券标题
     * "number": 66, // 优惠券数量
     * "limit_time_type": 2,// 有效期 当值为1时，取day值；当值为2时，取start_time 至 end_time
     * "day": "",// 如果不为空，则为 领取后day天有效
     * "start_time": "2019-05-16",
     * "end_time": "2019-06-20",
     * "type": 3, // 领取或兑换条件：1直接领取，2积分兑换，3余额兑换，4积分/余额兑换
     * "integral": "0.00",// 当type值为2时，只支持积分兑换； 当type值为4时，支持积分，也支持余额
     * "balance": "10.00",// 当type值为3时，只支持余额兑换； 当type值为4时，支持积分，也支持余额
     * "limit_number": 2, // 每人限制领取
     * "sort": 0,
     * "use_condition": 0,// 使用条件,0不限制，大于0 则为满m元-n元
     * "face_value": "20.00",// 优惠券面值
     * "create_time": "2019-05-26 16:49:38",
     * "update_time": "2019-05-26 16:49:38",
     * "delete_time": null
     * }
     * ],
     * "requestUrl": "/api/v1/coupon/read?action=2"
     * }
     */


    public function readCoupon($action = '')
    {
        $user_id = Token::getCurrentTokenUserId();
      	$merchant = input('merchid');
      	if(!empty($merchant)){
         	$result = \app\api\model\Coupon::readCouponnew($action, $user_id,$merchant);
        }else{
            $result = \app\api\model\Coupon::readCoupon($action, $user_id);
        }
        return $result;
    }

    /**
     * @api {post} coupon/receive  2、直接领取 / 兑换优惠券
     * @apiGroup coupon
     * @apiVersion 0.1.0
     * @apiDescription  首页礼包直接领取券，个人中心 积分/余额 兑换获取优惠券
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/coupon/receive
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} coupon_id 优惠券ID号
     * @apiParam  {int} type 1首页礼包直接领券，2积分兑换，3余额兑换
     * @apiName 28
     */
    public function receiveCoupon()
    {
        $user_id = Token::getCurrentTokenUserId();
        (new ReceiveCoupon())->goCheck();
        $post = input('post.');
        $coupon_id = $post['coupon_id'];
        $type = $post['type'];

        $coupon_info = \app\api\model\Coupon::get($coupon_id);
        $user_info = User::get($user_id);
        $limit_number = $coupon_info['limit_number'];
        $total_number = $coupon_info['number'];
        $mid = $coupon_info['merchant'];


        $user_receive_count = CouponLog::where('coupon', $coupon_id)->where('user', $user_id)->count();
        $users_receive_count = CouponLog::where('coupon', $coupon_id)->count();
        // 查询今日是否已领取过了
        $is_user_now_receive = CouponLog::where('coupon', $coupon_id)->where('user', $user_id)->where('status','0')->order('create_time desc')->find();

        if ($users_receive_count >= $total_number) {
            throw new ErrorMessage(['msg' => '优惠券已被抢光']);
        }
        if ($limit_number > 0) {
            if ($user_receive_count >= $limit_number) {
                throw new ErrorMessage(['msg' => '您领取该优惠券已达上限']);
            }
        }
        //echo json_encode($coupon_info);exit();
        // 领取张数 无限制 （n天有效）
        $end_time = GetMkTime($is_user_now_receive['create_time']) + $coupon_info['day']['day'] *24*60*60;

        if($end_time>time()){
            throw new ErrorMessage(['msg' => '已有相同优惠券可用']);
        }
      
        if ($type == 1) {
            if ($coupon_info['type'] != $type) {
                throw new ErrorMessage(['msg' => '非法请求']);
            }
        }
        if ($type == 2) {
            $user_integral = floatval($user_info['integral']);
            $coupon_integral = floatval($coupon_info['integral']);
            if ($type == $coupon_info['type']) {

            } else if ($coupon_info['type'] == 4) {

            } else {
                throw new ErrorMessage(['msg' => '非法请求']);
            }

            if (($user_integral - $coupon_integral) < 0) {
                throw new ErrorMessage(['msg' => '您的积分不足']);
            }
            $number = $coupon_integral;

        }
        if ($type == 3) {
            $user_balance = floatval($user_info['balance']);
            $coupon_balance = floatval($coupon_info['balance']);
            if ($type == $coupon_info['type']) {

            } else if ($coupon_info['type'] == 4) {

            } else {
                throw new ErrorMessage(['msg' => '非法请求']);
            }

            if (($user_balance - $coupon_balance) < 0) {
                throw new ErrorMessage(['msg' => '您的余额不足']);
            }
            $number = $coupon_balance;
        }

        $spikeData = $this->spikeCoupon($coupon_id, $user_id, $total_number);
        if (!$spikeData) {
            throw new ErrorMessage(['msg' => '优惠券已被抢光！']);
        }

        if ($type == 1) {
            $data = [
                'coupon' => $spikeData['cid'],
                'user' => $spikeData['user_id'],
                'coupon_id' => $spikeData['cid']
            ];
            CouponLog::create($data);
            throw new SuccessMessage(['msg' => '领取成功']);
        }

        if ($type == 2) {
            $dataIntegral = [
                'use_action' => 6,
                'type' => 2,
                'amount' => $number,
                'user' => $spikeData['user_id'],
                'source_type' => '1',
                'remark' => '兑换优惠券消费',
                'mid' => $mid
            ];
            IntegralLog::create($dataIntegral);
            User::where('id', $spikeData['user_id'])->setDec('integral', $number);
            $data = [
                'coupon' => $spikeData['cid'],
                'user' => $spikeData['user_id'],
                'coupon_id' => $spikeData['cid']
            ];
            CouponLog::create($data);
            throw new SuccessMessage(['msg' => '兑换成功']);
        }

        if ($type == 3) {
            $dataBalance = [
                'use_action' => 3,
                'type' => 2,
                'amount' => $number,
                'user' => $spikeData['user_id'],
                'source_type' => 4,
                'remark' => '兑换优惠券消费',
                'mid' => $mid
            ];
            BalanceLog::create($dataBalance);
            User::where('id', $spikeData['user_id'])->setDec('balance', $number);
            $data = [
                'coupon' => $spikeData['cid'],
                'user' => $spikeData['user_id'],
                'coupon_id' => $spikeData['cid']
            ];
            CouponLog::create($data);
            throw new SuccessMessage(['msg' => '兑换成功']);
        }

    }


    private function spikeCoupon($cid, $user_id, $limit_number)
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis_ms_status = 'ms_status' . $cid;
        $redis_ms_users = 'ms_users' . $cid;

        if ($redis->get($redis_ms_status) != 0) {
            return false;
        }

        $number = $redis->lLen($redis_ms_users);
        if ($number >= $limit_number) {
            $redis->set($redis_ms_status, 1);
            return false;
        }

        $redis->lPush($redis_ms_users, $user_id);
        return [
            'cid' => $cid,
            'user_id' => $user_id
        ];
    }


    /**
     * @api {post} coupon/user  3、我的优惠券
     * @apiGroup coupon
     * @apiVersion 0.1.0
     * @apiDescription  个人中心，我的已经领取的优惠券列表
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/coupon/user
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} type 0 未使用，1已使用
     * @apiName 29
     */
    public function userCoupon($type = '')
    {
        $user_id = Token::getCurrentTokenUserId();
        return CouponLog::userCoupon($user_id, $type);
    }
    public function bag(){
        $data = Db::name('coupon_bag')->field(['id','title','coupon','price'])->select();
        if(!empty($data)){
            foreach ($data as $k => $v) {
                $data[$k]['list'] = Db::name('coupon')
                ->alias('c')
                ->join('merchant m','c.merchant=m.id')
                ->field(['c.id','c.title','c.limit_time_type','c.start_time','c.end_time','c.day','c.use_condition','c.face_value','m.title as m_title'])
                ->where('c.id','in',$v['coupon'])
                ->select();
                foreach ($data[$k]['list'] as $k2 => $v2) {
                    $data[$k]['list'][$k2]['use_condition'] = intval($v2['use_condition']);
                    $data[$k]['list'][$k2]['face_value'] = intval($v2['face_value']);
                    $data[$k]['list'][$k2]['day'] = '领取后'.$v2['day'].'天内有效';
                }
                $data[$k]['list'] = array_date($data[$k]['list'],['start_time','end_time'],'Y-m-d');
            }
        }else{
            msg(0,'暂无数据');
        }
        showjson(['status'=>1,'msg'=>'获取卡包列表成功','data'=>$data]);
    }
    public function bag_pay(){
        $user_id = Token::getCurrentTokenUserId();
        $id = input('id');
        if(empty($id) || !is_numeric($id)){
            msg(0,'卡包id不能为空');
        }
        $id = intval($id);
        $bag_res = Db::name('coupon_bag')->where('id',$id)->find();
        if(input('is_small')){
            $data = [
                'user'=>$user_id,
                'merchant'=>0,
                'merchant_user'=>0,
                'coupon'=>0,
                'coupon_user'=>0,
                'integral'=>'0.00',
                'merchant_integral_deduction'=>0,
                'balance'=>'0.00',
                'total_amount'=>$bag_res['price'],
                'real_payment'=>$bag_res['price'],
                'order'=>getRandomNumber(6) . timeToStr(time(), 'YmdHis') . $user_id,
                'integral_rebate'=>'0.00',
                'integral_ratio'=>0,
                'status'=>0,
                'pay_type'=>1,
                'create_time'=>time(),
                'update_time'=>time(),
                'bag_id'=>$id,
            ];
            $res = Db::name('consumption_order')->insert($data);
            if(empty($res)){
                msg(0,'提交订单失败,请重试');
            }
            $appid = config('wx.appid'); //小程序appid
            $appsecret = config('wx.appsecret'); //小程序的secret
            $MCHID = config('wx.mchId'); //商户号id
            $KEY = config('wx.partnerKey'); //商户号key
            $total_fee = $data['real_payment'] * 100; //支付金额单位是分的，所以要乘100
            $openid = input('openid');
            $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
            $order_no = $data['order'];
            $data = [];
            $data['appid'] = $appid;  //小程序appid
            $data['mch_id'] = $MCHID;	//商户号id
            $data['nonce_str'] = md5($MCHID.time()); //验证的支付
            $data['openid'] = $openid; //用户openid
            $data['body'] = config('wx.description'); //微信支付对应的商家/公司主体名
            $data['out_trade_no'] = $order_no; //订单号id,用于回调改订单状态
            $data['total_fee'] = $total_fee; //支付金额，单位为分！！
            $data['spbill_create_ip'] = '8.8.8.8'; //验证ip地址，这个不用改随意
            $data['notify_url'] = is_https() . '/api/v2/' . config('wx.bagNotifyUrl'); //微信支付成功的回调路径，要写死这个路径，记得要是小程序允许访问的路径
            $data['trade_type'] = "JSAPI"; //小程序支付，所以是JSAPI
            //var_dump($data);
            ksort($data);
            $sign_str = ToUrlParams($data);
            $sign_str = $sign_str."&key=".$KEY;
            $data['sign'] = strtoupper(md5($sign_str));
            $xml = arrayToXml($data);
            $r = postXmlCurl($xml,$url,true);
            $result = json_decode(xml_to_json($r));
            //var_dump($result);exit();
            if($result->return_code == 'SUCCESS'){
                $sdata['appId'] = $appid;
                $sdata['timeStamp'] = time();
                $sdata['nonceStr'] = md5(time().rand().rand().$openid);
                $sdata['package'] = "prepay_id=".$result->prepay_id;
                $sdata['signType'] = "MD5";
                ksort($sdata);
                $sign_str = ToUrlParams($sdata);
                $sign_str = $sign_str."&key=".$KEY;
                $sdata['paySign'] = strtoupper(md5($sign_str));
                $sdata['order_no'] = $order_no;
                echo json_encode(['status'=>1,'data'=>$sdata,'msg'=>"请求成功"]);exit();
            }else{
                echo json_encode(['status'=>0,'msg'=>'支付异常']);exit();
            }
        }else{
            $is_balance = input('is_balance');
            if(!is_numeric($is_balance)){
                msg(0,'请传入正确的is_balance');
            }
            $is_balance = intval($is_balance);
            $type = input('type');
            if(!is_numeric($type)){
                msg(0,'请传入正确的支付方式');
            }
            $type = intval($type);
            if($type != 1 && $type != 2){
                msg(0,'请传入正确的支付方式');
            }
            $data = [
                'user'=>$user_id,
                'merchant'=>0,
                'merchant_user'=>0,
                'coupon'=>0,
                'coupon_user'=>0,
                'integral'=>'0.00',
                'merchant_integral_deduction'=>0,
                'order'=>getRandomNumber(6) . timeToStr(time(), 'YmdHis') . $user_id,
                'integral_rebate'=>'0.00',
                'integral_ratio'=>0,
                'create_time'=>time(),
                'update_time'=>time(),
                'bag_id'=>$id,
            ];
            if($is_balance){
                $user_res = Db::name('user')->field(['balance'])->where('id',$user_id)->find();
                //var_dump($user_res);exit();
                if($user_res['balance'] >= $bag_res['price']){
                    $price = 0;
                    $data['balance'] = $bag_res['price'];
                    $data['status'] = 1;
                    $data['total_amount'] = $bag_res['price'];
                    $data['real_payment'] = $price;
                }else{
                    //echo $bag_res['price'] . '!!' . $user_res['balance'];exit();
                    $price = ($bag_res['price']*100 - $user_res['balance']*100)/100;
                    //echo $price;exit();
                    $data['balance'] = $user_res['balance'];
                    $data['status'] = 0;
                    $data['total_amount'] = $bag_res['price'];
                    $data['real_payment'] = $price;
                }
            }else{
                $price = $bag_res['price'];
                $data['balance'] = 0.00;
                $data['status'] = 0;
                $data['total_amount'] = $price;
                $data['real_payment'] = $price;
            }
            if($price == 0){
                $res = Db::name('consumption_order')->insert($data);
                $insert_id = Db::name('consumption_order')->getLastInsID();
                if($res){
                    Db::name('user')->where('id',$user_id)->update(['balance'=>$user_res['balance']-$bag_res['price']]);
                    $res = Db::name('consumption_order')->where('id',$insert_id)->find();
                    //var_dump($res);exit();
                    $data = Db::name('coupon')->where('id','in',Db::name('coupon_bag')->where('id',$res['bag_id'])->find()['coupon'])->select();
                    foreach ($data as $k => $v) {
                        $insert_data = [
                            'coupon'=>$v['id'],
                            'coupon_id'=>$v['id'],
                            'user'=>$res['user'],
                            'status'=>0,
                            'create_time'=>time(),
                            'update_time'=>time()
                        ];
                        Db::name('coupon_log')->insert($insert_data);
                    }
                    msg(2,'支付成功');
                }else{
                    msg(0,'提交订单失败,请重试');
                }
            }else{
                $data['pay_type'] = $type;
                //return $data;
                $res = Db::name('consumption_order')->insert($data);
                if(!$res){
                    msg(0,'提交订单失败,请重试');
                }
                if($type == 1){
                    $resultData = [
                        'apiKey' => config('wx.apiKey'),
                        'mchId' => config('wx.mchId'),
                        'partnerKey' => config('wx.partnerKey'),
                        'notifyUrl' => is_https() . '/api/v2/' . config('wx.bagNotifyUrl'),
                        'description' => config('wx.description'),
                        'totalFee' => $data['real_payment'] * 100,
                        'tradeNo' => $data['order'],
                        'amount' => $data['real_payment']
                    ];
                    showjson(['status'=>1,'msg'=>'微信支付','data'=>$resultData]);
                    //返回app微信参数
                }else{
                    //返回app支付宝参数
                    $resultData = [
                        'partner' => config('ali.partner'),
                        'seller' => config('ali.seller'),
                        'out_trade_no' => $data['order'],
                        'subject' => '安徽省金寿州酒店管理有限公司',
                        'body' => '卡包支付',
                        'total_fee' => $data['real_payment'],
                        'notify_url' => is_https() . '/api/v2/' . config('ali.bagNotifyUrl'),
                        'rsaPriKey' => config('ali.rsaPriKey'),
                        'rsaPubKey' => config('ali.rsaPubKey'),
                    ];
                    showjson(['status'=>1,'msg'=>'支付宝支付','data'=>$resultData]);
                }
            }
        }
    }
    //冗余代码 可删除
    public function bag_pay_status(){
        $user_id = Token::getCurrentTokenUserId();
        $ordersn = input('ordersn');
        if(empty($ordersn)){
            msg(0,'请传入正确的订单号');
        }
        Db::name('consumption_order')
        ->field(['status'])
        ->where('order',$ordersn)
        ->where('user',$user_id)
        ->find()['status'] ? msg(1,'支付成功') : msg(0,'支付失败');
    }
}
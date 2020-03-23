<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/8/10
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v2;


use app\api\controller\v1\BaseController;
use app\api\model\BalanceLog;
use app\api\model\ConsumptionOrder;
use app\api\model\Coupon;
use app\api\model\CouponLog;
use app\api\model\IntegralLog;
use app\api\model\Merchant;
use app\api\model\User;
use app\api\service\Token;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ValidAmount;
use think\Db;
use think\Exception;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'mustBeGet' => ['only' => 'offlinepay'],
        'mustBePost' => ['only' => ['selectcoupon', 'paypassword', 'confirmorder', 'userrechargebalance']],
    ];

    // 用户扫码商家二维码(文本二维码，参数是mid), 用户消费，商家收款界面
    //
    // http://sxr.ijiandian.com/api/v1/pay/offline?mid=1

    // 使用流程：
    // 1.前端扫描二维码(内容：参数mid=1)，扫描成功回调，转跳到向商家收款界面。
    // 2.收款界面加载时，调以下接口


    /**
     * @apiDefine  pay 支付
     */

    /**
     * @api {get} pay/offline  1、付款页面初始化
     * @apiGroup pay
     * @apiVersion 0.1.0
     * @apiDescription 页面初始化 --当扫描成功后跳转到用户付款商家收款界面，并在加载此页面时调用此接口
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/pay/offline
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {string} mid 扫描识别出来的字符
     * @apiName 32
     * @apiSuccess {json} data 页面初始化信息
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     * {
     * "msg": "success",
     * "code": 20000,
     * "data": {
     * "coupon": [ // 优惠券列表，下拉选项赋值，title=coupon.coupon.title  value=coupon.id+','+coupon.coupon.id
     * {
     * "id": 1,
     * "coupon": {
     * "id": 3,
     * "merchant": {
     * "id": 1,
     * "title": "寿州国际酒店",
     * "img": {
     * "id": 71,
     * "url": "http://sxr.ijiandian.com/uploads/admin/20190518/a5b77cd7003dfca59e0700c4078ace1e.jpg"
     * },
     * "qrcode_url": "http://ijiandian.com",
     * "type": 3,
     * "user": 1,
     * "integral": 0,
     * "proportion": 0,
     * "address": "寿县寿春镇南门大圆盘，近明珠大道和下关交叉口"
     * },
     * "title": "5元优惠券",
     * "limit_time_type": 1,
     * "day": {
     * "day": 20,
     * "str": "领取后20天内有效"
     * },
     * "start_time": "",
     * "end_time": "",
     * "use_condition": {
     * "amount": 800,
     * "msg": "消费满800元减5元"
     * },
     * "face_value": "5.00"
     * },
     * "status": {
     * "status": 0,
     * "end_time": "截止2019-06-16",
     * "msg": "未使用"
     * },
     * "create_time": "2019-05-27 19:09:42"
     * },
     * {
     * "id": 2,
     * "coupon": {
     * "id": 3,
     * "merchant": {
     * "id": 1,
     * "title": "寿州国际酒店",
     * "img": {
     * "id": 71,
     * "url": "http://sxr.ijiandian.com/uploads/admin/20190518/a5b77cd7003dfca59e0700c4078ace1e.jpg"
     * },
     * "qrcode_url": "http://ijiandian.com",
     * "type": 3,
     * "user": 1,
     * "integral": 0,
     * "proportion": 0,
     * "address": "寿县寿春镇南门大圆盘，近明珠大道和下关交叉口"
     * },
     * "title": "5元优惠券",
     * "limit_time_type": 1,
     * "day": {
     * "day": 20,
     * "str": "领取后20天内有效"
     * },
     * "start_time": "",
     * "end_time": "",
     * "use_condition": {
     * "amount": 800,
     * "msg": "消费满800元减5元"
     * },
     * "face_value": "5.00"
     * },
     * "status": {
     * "status": 0,
     * "end_time": "截止2019-06-16",
     * "msg": "未使用"
     * },
     * "create_time": "2019-05-27 19:13:04"
     * },
     * {
     * "id": 3,
     * "coupon": {
     * "id": 6,
     * "merchant": {
     * "id": 1,
     * "title": "寿州国际酒店",
     * "img": {
     * "id": 71,
     * "url": "http://sxr.ijiandian.com/uploads/admin/20190518/a5b77cd7003dfca59e0700c4078ace1e.jpg"
     * },
     * "qrcode_url": "http://ijiandian.com",
     * "type": 3,
     * "user": 1,
     * "integral": 0,
     * "proportion": 0,
     * "address": "寿县寿春镇南门大圆盘，近明珠大道和下关交叉口"
     * },
     * "title": "给17681125543发20元优惠券",
     * "limit_time_type": 2,
     * "day": "",
     * "start_time": "2019-05-16",
     * "end_time": "2019-06-20",
     * "use_condition": {
     * "amount": "0.00",
     * "msg": ""
     * },
     * "face_value": "20.00"
     * },
     * "status": {
     * "status": 0,
     * "end_time": "2019-05-16 至 2019-06-20",
     * "msg": "未使用"
     * },
     * "create_time": "2019-05-27 22:07:37"
     * }
     * ],
     * "user": { // 用户信息
     * "integral": 31,// 可以用积分
     * "balance": 55,// 可用余额
     * "point_deduction": 0 //大于0显示积分选项，否则隐藏，并当前值为积分抵扣人民币的值
     * },
     * "merchant": { // 商家信息
     * "title": "寿州国际酒店"
     * }
     * },
     * "requestUrl": "http://sxr.ijiandian.com/api/v1/pay/offline?mid=1"
     * }
     */
    public function offlinePay()
    {
        $user_id = Token::getCurrentTokenUserId();
        $mid = input('get.mid');
        if (empty($mid))
            throw new ErrorMessage([
                'msg' => '必填参数不能为空'
            ]);
        $mid = findNum(encryptDecrypt(config('qrcode.qrcode_salt'), $mid, 0));
        $result = CouponLog::userAvailableCoupon($user_id, $mid);
        throw new SuccessMessage([
            'data' => $result
        ]);
    }


    /**
     * @api {post} pay/select/coupon  2、优惠券列表
     * @apiGroup pay
     * @apiVersion 0.1.0
     * @apiDescription 当用户输入总消费金额时，调用此接口给下拉优惠券赋值，取值方式同接口 1、付款页面初始化
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/pay/select/coupon
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {string} mid 初始化完成后 merchant 里的id
     * @apiParam  {int} total 总消费金额
     * @apiName 33
     */
    public function selectCoupon()
    {
        $user_id = Token::getCurrentTokenUserId();
        $mid = input('post.mid');
        $total = input('post.total');
        if (empty($mid) || empty($total))
            throw new ErrorMessage([
                'msg' => '必填参数不能为空'
            ]);
        $result = CouponLog::selectCoupon($user_id, $mid, $total);
        throw new SuccessMessage([
            'data' => $result
        ]);
    }


    /**
     * @api {post} pay/password  3、支付密码
     * @apiGroup pay
     * @apiVersion 0.1.0
     * @apiDescription 当点击确认支付时调用此接口
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/pay/password
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} pay_pwd 支付密码
     * @apiName 35
     */
    public function payPassword()
    {
        $user_id = Token::getCurrentTokenUserId();
        $pay_pwd = md5(input('post.pay_pwd') . config('md5.user_pay_password_salt'));
        $result = User::where('id', $user_id)
            ->where('pay_pwd', $pay_pwd)
            ->find();
        if (!$result)
            throw new ErrorMessage([
                'msg' => '支付密码错误'
            ]);
        throw new SuccessMessage();
    }

    /**
     * @api {post} pay/confirmorder  4、确认支付
     * @apiGroup pay
     * @apiVersion 0.1.0
     * @apiDescription 当点击确认支付，且支付密码输入正确时调用此接口
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/pay/confirmorder
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} total 总消费金额
     * @apiParam  {int} mid 商户id
     * @apiParam  {string} cid 优惠券id,格式（用户领取券id,优惠券id == 1,2） 没有选择优惠券传 （0,0）不懂请咨询我
     * @apiParam  {int} is_integral 是否勾选积分，未勾选传0 勾选传1
     * @apiParam  {int} is_balance 是否勾选余额，未勾选传0 勾选传1
     * @apiName 36
     */
    public function confirmOrder()
    {
        $user_id = Token::getCurrentTokenUserId();
        $post = input('post.');
        if ($post['total'] <= 0) {
            throw new SuccessMessage(['msg' => '请输入消费总金额']);
        }
        $result = $this->confirmOrderCopy($post, $user_id);
        if ($result) {
            throw new SuccessMessage(['msg' => '支付成功','data'=>['order_no'=>$result]]);
        } else {
            throw new ErrorMessage(['msg' => '支付失败，请重新发起支付']);
        }
    }

    public function confirmOrderCopy($post, $user_id)
    {

        // mid 商家id cid 优惠券id   积分integral 余额balance

        $cids = explode(",", $post['cid']);
        //var_dump($cids);exit();
        $coupon_log_id = $cids[0]; // 优惠券用户领取表
        $coupon_id = $cids[1]; // 优惠券表
        $is_integral = $post['is_integral'];// 是否选了积分
        $is_balance = $post['is_balance'];// 是否选了余额

        $mid = $post['mid']; // 商家id 知道到哪家去消费了
        $merchantInfo = Merchant::get($mid); // 商家信息
        $userInfo = User::get($user_id);// 用户信息


        $couponAmount = $this->deductionCouponAmount($coupon_id);
        $integralAmount = $this->deductionIntegralAmount($is_integral, $merchantInfo, $userInfo);
        $balanceAmount = $this->deductionBalanceAmount($is_balance, $userInfo);


        $total = $post['total'];
        $total_copy = $post['total'];


        $boolean = true;
        if ($couponAmount > 0) {
            $total = $total - $couponAmount;
            if ($total <= 0) {
                $boolean = false;
            } else {
                $boolean = true;
            }
        }

        $deduct_integral = 0;
        if ($boolean == true) {
            if ($integralAmount > 0) {
                $total = $total - $integralAmount;
                if ($total <= 0) {
                    $boolean = false;
                    $deduct_integral = $integralAmount - abs($total);
                } else {
                    $boolean = true;
                    $deduct_integral = $integralAmount;
                }
            }
        }
        // total 80  $deduct_integral =20
        $deduct_balance = 0;
        if ($boolean == true) {
            if ($balanceAmount > 0) {
                $total = $total - $balanceAmount;
                if ($total <= 0) {
                    $boolean = false;
                    $deduct_balance = $balanceAmount - abs($total);
                } else {
                    $boolean = true;
                    $deduct_balance = $balanceAmount;
                }
            }
        }

        if ($boolean) {
            $total;
        } else {
            $total;
        }

        if ($total > 0) {
            // 支付不足 需要额外实付金额  $total
            if (!$post['type']) {
                throw new SuccessMessage([
                    'msg' => '请选择支付方式',
                    'data' => [
                        'premium' => sprintf("%.2f",$total)
                    ]
                ]);
            }

            // 是否用了优惠券
            if ($couponAmount > 0) {
                // 回调里需要改变用户优惠券使用状态 以下大段注释就是要操作的哦
                /*CouponLog::where('id', $coupon_log_id)->update(['status' => 1, 'use_time' => time()]);*/
                $coupon_id = $coupon_id;
                $coupon_log_id = $coupon_log_id;
            } else {
                $coupon_id = 0;
                $coupon_log_id = 0;
            }


            // 是否用了积分
            if ($deduct_integral !== 0) {
                // 支付回调里需要新增积分消费记录 + 用户积分减少操作 以下大段就是回调里要操作的哦

                /*$integralData = [
                    'use_action' => 4,
                    'type' => 2,
                    'amount' => $deduct_integral * floatval($merchantInfo['proportion']),
                    'user' => $user_id,
                    'source_type' => 1,
                    'remark' => '扫码付款消费',
                    'mid' => $mid
                ];
                IntegralLog::create($integralData);
                User::where('id', $user_id)->setDec('integral', $deduct_integral * floatval($merchantInfo['proportion']));*/

                $integral = $deduct_integral * floatval($merchantInfo['proportion']);
            } else {
                $integral = 0;
            }

            // 是否用了余额
            if ($deduct_balance !== 0) {
                // 回调里需要 新增余额消费记录 + 用户余额减少操作 以下大段注释就是要操作的哦
                /*$balanceData = [
                    'use_action' => 1,
                    'type' => 2,
                    'amount' => $deduct_balance,
                    'user' => $user_id,
                    'source_type' => 4,
                    'remark' => '扫码付款消费',
                    'mid' => $mid
                ];
                BalanceLog::create($balanceData);
                User::where('id', $user_id)->setDec('balance', $deduct_balance);*/

                $deduct_balance = $deduct_balance;
            } else {
                $deduct_balance = 0;
            }

            // 是否支持消费返积分
            $proportion = floatval($merchantInfo['integral']);
            if ($proportion > 0) {
                //返利总金额 =  实付金额+第三方支付金额
                $rebateTotalAmount = $total + $deduct_balance;
                if ($rebateTotalAmount > 0) {
                    $rebateAmount = $rebateTotalAmount / 100 * $proportion;
                    // 回调里需要 新增积分消费返利记录 + 用户积分增加操作 以下大段注释就是要操作的哦
                    /*$integralData = [
                        'use_action' => 5,
                        'type' => 1,
                        'amount' => $rebateAmount,
                        'user' => $user_id,
                        'source_type' => 1,
                        'remark' => '扫码付款消费返利',
                        'mid' => $mid
                    ];
                    IntegralLog::create($integralData);
                    User::where('id', $user_id)->setInc('integral', $rebateAmount);*/
                    $integral_rebate = $rebateAmount;
                    $integral_ratio = $proportion;
                }
            } else {
                $integral_rebate = 0;
                $integral_ratio = 0;
            }

            // 准备数据，
            $order = getRandomNumber(6) . timeToStr(time(), 'YmdHis') . $user_id;
            $preData = [
                'user' => $user_id,// 用户id
                'merchant' => $merchantInfo['id'],//商家id
                'merchant_user' => $merchantInfo['user'],// 商家用户id
                'coupon' => $coupon_id,//优惠券id
                'coupon_user' => $coupon_log_id,// 用户领取 优惠券id
                'integral' => $integral,// 消费积分
                'merchant_integral_deduction' => $merchantInfo['proportion'],// 商家当前积分抵扣值
                'balance' => $deduct_balance,// 余额
                'total_amount' => $total_copy,// 总金额
                'real_payment' => sprintf("%.2f",$total),// 实付金额 第三方支付
                'order' => $order,// 订单号
                'integral_rebate' => $integral_rebate,// 消费返利积分
                'integral_ratio' => $integral_ratio,// 积分比例
                'status' => 0, // 订单状态
                'pay_type' => $post['type'] // 支付来源
            ];
            // 插入数据库
            $result = $this->initializationConsumptionOrderPre($preData, $post['type']);
            if (!$result) {
                throw new ErrorMessage(['msg' => '请重新发起支付']);
            }
            throw new SuccessMessage([
                'msg' => '第三方支付',
                'data' => $result
            ]);


        } else {

            Db::startTrans();
            try {

                $order = $this->initializationConsumptionOrder($user_id, $total_copy, $merchantInfo);              
                if ($couponAmount > 0) {
                    CouponLog::where('id', $coupon_log_id)->update(['status' => 1, 'use_time' => time()]);
                    ConsumptionOrder::where('order', $order)->update(['coupon' => $coupon_id, 'coupon_user' => $coupon_log_id]);
                }

                if ($deduct_integral !== 0) {

                    $integralData = [
                        'use_action' => 4,
                        'type' => 2,
                        'amount' => $deduct_integral * floatval($merchantInfo['proportion']),
                        'user' => $user_id,
                        'source_type' => 4,
                        'remark' => '扫码付款消费',
                        'mid' => $mid
                    ];
                    IntegralLog::create($integralData);
                    ConsumptionOrder::where('order', $order)->update(['integral' => $deduct_integral * floatval($merchantInfo['proportion'])]);
                    User::where('id', $user_id)->setDec('integral', $deduct_integral * floatval($merchantInfo['proportion']));
                }


                if ($deduct_balance !== 0) {
                    $balanceData = [
                        'use_action' => 1,
                        'type' => 2,
                        'amount' => $deduct_balance,
                        'user' => $user_id,
                        'source_type' => 4,
                        'remark' => '扫码付款消费',
                        'mid' => $mid
                    ];
                    BalanceLog::create($balanceData);
                    ConsumptionOrder::where('order', $order)->update(['balance' => $deduct_balance]);
                    User::where('id', $user_id)->setDec('balance', $deduct_balance);
                }


                $proportion = floatval($merchantInfo['integral']);
                if ($proportion > 0) {

                    if ($deduct_balance > 0) {
                        $rebateAmount = $deduct_balance / 100 * $proportion;
                        $integralData = [
                            'use_action' => 5,
                            'type' => 1,
                            'amount' => $rebateAmount,
                            'user' => $user_id,
                            'source_type' => 4,
                            'remark' => '扫码付款消费返利',
                            'mid' => $mid
                        ];
                        IntegralLog::create($integralData);
                        User::where('id', $user_id)->setInc('integral', $rebateAmount);
                        ConsumptionOrder::where('order', $order)->update(['integral_rebate' => $rebateAmount, 'integral_ratio' => $proportion]);
                    }

                }

                Db::commit();
                return $order;

            } catch (Exception $ex) {
                Db::rollback();
                return false;
            }

        }
    }


    // 扣除优惠券的金额
    private function deductionCouponAmount($coupon_id)
    {
        //var_dump($coupon_id);exit();
        $coupon_info = Coupon::get($coupon_id);
        if (!empty($coupon_info)) {
            $face_value = $coupon_info['face_value'];
        } else {
            $face_value = 0;
        }
        return floatval($face_value);
    }

    // 扣除积分==人民币 金额
    private function deductionIntegralAmount($is_integral, $merchantInfo, $userInfo)
    {
        $is_integral_deduction = floatval($merchantInfo['proportion']);
        if ($is_integral == '1') {
            if ($is_integral_deduction != 0) {
                $integral = floatval($userInfo['integral']) / $is_integral_deduction;
            } else {
                $integral = 0;
            }
        } else {
            $integral = 0;
        }
        return floatval($integral);
    }

    // 扣除余额的 金额
    private function deductionBalanceAmount($is_balance, $userInfo)
    {
        if ($is_balance == '1') {
            $balance = $userInfo['balance'];
        } else {
            $balance = 0;
        }
        return floatval($balance);
    }

    // 初始化订单(资金充足的情况下)
    private function initializationConsumptionOrder($user_id, $total, $merchantInfo)
    {
        $order = getRandomNumber(6) . timeToStr(time(), 'YmdHis') . $user_id;
        ConsumptionOrder::create([
            'user' => $user_id,
            'merchant' => $merchantInfo['id'],
            'merchant_user' => $merchantInfo['user'],
            'total_amount' => $total,
            'order' => $order,
            'merchant_integral_deduction' => $merchantInfo['proportion'],
            'pay_type' =>3
        ]);
        return $order;
    }

    private function initializationConsumptionOrderPre($data, $type)
    {
        $result = ConsumptionOrder::create($data);
        if ($result) {
            //小程序支付
            if(input('is_small')){
                $appid = config('wx.appid'); //小程序appid
                $appsecret = config('wx.appsecret'); //小程序的secret
                $MCHID = config('wx.mchId'); //商户号id
                $KEY = config('wx.partnerKey'); //商户号key
                $res = Db::name('consumption_order')->where('order',$data['order'])->find();
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
                $data['out_trade_no'] = $res['order']; //订单号id,用于回调改订单状态
                $data['total_fee'] = $total_fee; //支付金额，单位为分！！
                $data['spbill_create_ip'] = '8.8.8.8'; //验证ip地址，这个不用改随意
                $data['notify_url'] = is_https() . '/api/v2/' . config('wx.payNotifyUrl'); //微信支付成功的回调路径，要写死这个路径，记得要是小程序允许访问的路径
                $data['trade_type'] = "JSAPI"; //小程序支付，所以是JSAPI
        
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
            }else{//app支付
                // 微信支付
                if ($type == '1') {
                    $resultData = [
                        'apiKey' => config('wx.apiKey'),
                        'mchId' => config('wx.mchId'),
                        'partnerKey' => config('wx.partnerKey'),
                        'notifyUrl' => is_https() . '/api/v2/' . config('wx.payNotifyUrl'),
                        'description' => config('wx.description'),
                        'totalFee' => $data['real_payment'] * 100,
                        'tradeNo' => $data['order'],
                        'amount' => $data['real_payment']
                    ];
                } else {// 支付宝支付
                    $resultData = [
                        'partner' => config('ali.partner'),
                        'seller' => config('ali.seller'),
                        'out_trade_no' => $data['order'],
                        'subject' => '安徽省金寿州酒店管理有限公司',
                        'body' => '扫描支付',
                        'total_fee' => $data['real_payment'],
                        'notify_url' => is_https() . '/api/v2/' . config('ali.payNotifyUrl'),
                        'rsaPriKey' => config('ali.rsaPriKey'),
                        'rsaPubKey' => config('ali.rsaPubKey'),
                    ];
                }
            }

            return $resultData;
        }
        return false;
    }

    // 初始化预订单(资金不足的情况)

    /**
     * @api {post} pay/user/recharge  5、余额充值
     * @apiGroup pay
     * @apiVersion 0.1.0
     * @apiDescription  用户充值 支持微信/支付宝
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/pay/user/recharge
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} amount 充值金额
     * @apiParam  {int} type 类型 1微信，2支付宝
     * @apiParam  {int} order 订单号
     * @apiName 37
     */
    public function userRechargeBalance()
    {
        // 写日志
//        $time = date("Y-m-d H:i:s", time());
//        $myfile = fopen($time . ".txt", "w") or die("Unable to open file!");
//        $input = file_get_contents('php://input');
//
//        fwrite($myfile, serialize($input) . PHP_EOL);
//        fclose($myfile);

        $user_id = Token::getCurrentTokenUserId();
        (new ValidAmount())->goCheck();
        $post = input('post.');
        if ($post['type'] != '1' && $post['type'] != '2') {
            throw new ErrorMessage(['msg' => '充值类型错误']);
        }
        // 避免重复充值
        $order = $post['order'];
        $result = BalanceLog::where('order', $order)->find();
        if ($result) {
            throw new SuccessMessage(['msg' => '充值成功']);
        }

        $result = BalanceLog::userRechargeBalance($post, $user_id);
        if ($result) {
            throw new SuccessMessage(['msg' => '充值成功']);
        }
        throw new ErrorMessage(['msg' => '操作失败，请重新发起充值']);

    }

    /**
     * @method 充值预付订单, 参数 type 充值类型， amount 充值金额
     * @throws ErrorMessage
     * @throws SuccessMessage
     * @throws \app\lib\exception\ParameterException
     */
    public function rechargePre()
    {

        $user_id = Token::getCurrentTokenUserId();
        // 参数 金额+充值类型+用户id
        (new ValidAmount())->goCheck();
        $post = input('post.');
        if ($post['type'] != '1' && $post['type'] != '2') {
            throw new ErrorMessage(['msg' => '非法充值类型']);
        }
        // 开始充值
        // 1. 生成订单号
        $order_no = getRandomNumber(6) . timeToStr(time(), 'YmdHis') . $user_id;
        // 2. 准备余额记录表充值预订单记录
        $type = $post['type'] == 1 ? '微信' : '支付宝';
        $data = [
            'use_action' => 2,
            'type' => 1,
            'order' => $order_no,
            'amount' => $post['amount'],
            'user' => $user_id,
            'source_type' => $post['type'],
            'remark' => '用户使用' . $type . '充值',
            'status' => 0
        ];


        // 3. 成功返回订单号信息，让客户端发起支付
        $result = BalanceLog::create($data);
        if ($result) {
            // 微信返回客户端信息处理
            $resultWxData = [
                'apiKey' => config('wx.apiKey'),
                'mchId' => config('wx.mchId'),
                'partnerKey' => config('wx.partnerKey'),
                'notifyUrl' => is_https() . '/api/v2/' . config('wx.rechargeNotifyUrl'),
                'description' => config('wx.description'),
                'totalFee' =>  $post['amount'] * 100,//sprintf("%.2f",$post['amount'] * 100),
                'tradeNo' => $order_no,
            ];
//            $resultAliData1 = [
//                'service'=>'mobile.securitypay.pay',
//                'partner' => config('ali.partner'),
//                '_input_charset'=>'UTF-8',
//                'notify_url' => is_https() . '/api/v1/' . config('ali.rechargeNotifyUrl'),
//                'out_trade_no'=>$order_no,
//                'subject'=>'金寿州国际大酒店',
//                'payment_type'=>1,
//                'seller_id' => config('ali.seller'),
//                'total_fee' => $post['amount'],
//            ];

            $resultAliData = [
                'partner' => config('ali.partner'),
                'seller' => config('ali.seller'),
                'out_trade_no' => $order_no,
                'subject' => '安徽省金寿州酒店管理有限公司',
                'body' => '余额充值',
                'total_fee' => sprintf("%.2f",$post['amount']),
                'notify_url' => is_https() . '/api/v2/' . config('ali.rechargeNotifyUrl'),
                'rsaPriKey' => config('ali.rsaPriKey'),
                'rsaPubKey' => config('ali.rsaPubKey'),

            ];
//            $aliResultSort = argSort($alidata);//排序
//            $aliSpliceStr = createLinkstring($aliResultSort);//&拼接
            // 支付宝返回给客户端处理
//            $resultSort = argSort($resultAliData);//排序
//            $spliceStr = createLinkstring($resultSort);//&拼接
//            $sign = rsaSign($spliceStr,'../extend/ali/rsa_private_key.pem');// 签名
//            $sign = urlencode($sign);
//            $orderInforStr = $aliSpliceStr."&sign=".$sign."&sign_type=RSA";
//            $orderInfor =[
//                'data'=>$alidata,
//            ];
            $resultData = $post['type'] == 1 ? $resultWxData : $resultAliData;
            throw new SuccessMessage(['data' => $resultData]);
        }
        throw new ErrorMessage(['msg' => '操作失败，请重新发起支付']);
    }


    // 订单充值状态查询
    public function getOrderStatus($order = '')
    {
        $user_id = Token::getCurrentTokenUserId();
        $orderInfo = BalanceLog::where('user', $user_id)->where('order', $order)->find();
        if ($orderInfo['status']) {
            throw new SuccessMessage();
        }
        throw new ErrorMessage();
    }

    // 微信充值回调
    public function wxPayRechargeNotify()
    {
        // return $this->return_xml_success('OK');
        $input = file_get_contents('php://input');
        if (!empty($input) && empty($_GET['out_trade_no'])) {
            $obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
            $data = json_decode(json_encode($obj), true);
            if ($data['result_code'] == 'SUCCESS') {
                // 商户订单号 $data['out_trade_no']
                // 以下处理数据库相关操作，需要做事务处理

                $result = BalanceLog::userRechargeBalanceWxPayNotify($data['out_trade_no'], 1);
                if ($result) {
                    return $this->return_xml_success('OK');
                } else {
                    return $this->return_xml_success('更改状态失败');
                }
            }
        }
    }
    //微信卡包回调
    public function wxbagNotify(){
        $data = file_get_contents('php://input');
        $msg = (array)simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
        // $msg = json_decode('{"appid":"wx87b9118427e1082c","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"N","mch_id":"1525644111","nonce_str":"a6241e1f7dea2b2a72638cb1dced4865","openid":"oBAFI4wrWaLRWg2Xp1K_O1_hPeRA","out_trade_no":"587449201911271123182","result_code":"SUCCESS","return_code":"SUCCESS","sign":"346817E0A97B8F0210098B71D59D5186","time_end":"20191127112700","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000429201911272345233118"}
        // ',true);
        if($msg['result_code'] == "SUCCESS") {
            // 支付成功这里要做的操作！
            $myfile = fopen(__DIR__."/notify.txt", "a");
            fwrite($myfile, json_encode($msg)."\n");
            fclose($myfile);
            $res = Db::name('consumption_order')->where('status',0)->where('order',$msg['out_trade_no'])->find();
            if(!empty($res)){
                $user_res = Db::name('user')->field(['balance'])->where('id',$res['user'])->find();
                Db::name('user')->where('id',$res['user'])->update(['balance'=>$user_res['balance']-$res['balance']]);
                Db::name('consumption_order')->where('order',$msg['out_trade_no'])->update(['status'=>1,'update_time'=>time()]);
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
                //var_dump($data);exit();
            }
        }
        echo '<xml>
          <return_code><![CDATA[SUCCESS]]></return_code>
          <return_msg><![CDATA[OK]]></return_msg>
        </xml>';
    }
    //支付宝卡包回调
    public function alibagNotify(){
        $post = input('post.');
        if ($post) {
            // 支付成功
            if ($post['trade_status'] == 'TRADE_SUCCESS') {
                $myfile = fopen(__DIR__."/alinotify.txt", "a");
                fwrite($myfile, json_encode($post)."\n");
                fclose($myfile);
                $res = Db::name('consumption_order')->where('status',0)->where('order',$post['out_trade_no'])->find();
                if(!empty($res)){
                    $user_res = Db::name('user')->field(['balance'])->where('id',$res['user'])->find();
                    Db::name('user')->where('id',$res['user'])->update(['balance'=>$user_res['balance']-$res['balance']]);
                    Db::name('consumption_order')->where('order',$post['out_trade_no'])->update(['status'=>1,'update_time'=>time()]);
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
                }
            }
        }
        echo 'success';
    }
    // 微信支付回调
    public function wxPayNotify()
    {
        // return $this->return_xml_success('OK');
        $input = file_get_contents('php://input');
        if (!empty($input) && empty($_GET['out_trade_no'])) {
            $obj = simplexml_load_string($input, 'SimpleXMLElement', LIBXML_NOCDATA);
            $post = json_decode(json_encode($obj), true);
            if ($post['result_code'] == 'SUCCESS') {
                // 商户订单号 $data['out_trade_no']
                // 以下处理数据库相关操作，需要做事务处理


                Db::startTrans();
                try {
                    // 预支付订单信息
//                    $consumptionOrderInfo = ConsumptionOrder::where('order', $post['out_trade_no'])->find();
                    $consumptionOrderInfo = Db::table('ijiandian_consumption_order')->where('order',$post['out_trade_no'])->find();
                    if($consumptionOrderInfo){
                        if($consumptionOrderInfo['status']==0){
                            // 优惠券
                            if ($consumptionOrderInfo['coupon_user']!==0) {
                                Db::table('ijiandian_coupon_log')->where('id', $consumptionOrderInfo['coupon_user'])->update(['status' => 1, 'use_time' => time()]);
                            }
                            // 积分
                            if ($consumptionOrderInfo['integral'] > 0) {
                                $integralData = [
                                    'use_action' => 4,
                                    'type' => 2,
                                    'amount' => $consumptionOrderInfo['integral'],
                                    'user' => $consumptionOrderInfo['user'],
                                    'source_type' => 1, // 来源
                                    'remark' => '扫码付款消费',
                                    'mid' => $consumptionOrderInfo['merchant'],
                                    'create_time'=>time(),
                                    'update_time'=>time(),
                                ];
                                Db::name('integral_log')->insert($integralData);
                                Db::table('ijiandian_user')->where('id',$consumptionOrderInfo['user'])->setDec('integral', $consumptionOrderInfo['integral']);
                            }

                            // 余额
                            if ($consumptionOrderInfo['balance'] > 0) {
                                $balanceData = [
                                    'use_action' => 1,
                                    'type' => 2,
                                    'amount' => $consumptionOrderInfo['balance'],
                                    'user' => $consumptionOrderInfo['user'],
                                    'source_type' => 1,
                                    'remark' => '扫码付款消费',
                                    'mid' => $consumptionOrderInfo['merchant'],
                                    'create_time'=>time(),
                                    'update_time'=>time(),
                                ];
                                Db::name('balance_log')->insert($balanceData);
                                Db::table('ijiandian_user')->where('id',$consumptionOrderInfo['user'])->setDec('balance', $consumptionOrderInfo['balance']);
                            }

                            // 返利
                            if ($consumptionOrderInfo['integral_rebate'] > 0) {
                                $integralData = [
                                    'use_action' => 5,
                                    'type' => 1,
                                    'amount' => $consumptionOrderInfo['integral_rebate'],
                                    'user' => $consumptionOrderInfo['user'],
                                    'source_type' => 1,
                                    'remark' => '扫码付款消费返利',
                                    'mid' => $consumptionOrderInfo['merchant'],
                                    'create_time'=>time(),
                                    'update_time'=>time(),
                                ];
                                Db::name('integral_log')->insert($integralData);
                                Db::table('ijiandian_user')->where('id', $consumptionOrderInfo['user'])->setInc('integral', $consumptionOrderInfo['integral_rebate']);

                            }
                            // 修改预支付 订单状态
                            Db::name('consumption_order')->where('order',$post['out_trade_no'])->update(['status'=>1]);

                        }
                    }

                    Db::commit();
                    return $this->return_xml_success('OK');

                } catch (Exception $ex) {
                    Db::rollback();
                    return $this->return_xml_success('更改状态失败');
                }
            }
        }
    }


    // 支付宝充值回调
    public function aliPayRechargeNotify()
    {

        // 写日志
//        $time = 'ali' . date("Y-m-d H:i:s", time());
//        $myfile = fopen($time . ".txt", "w") or die("Unable to open file!");
//
//        $input = file_get_contents('php://input');
//        fwrite($myfile, $input['trade_status'] . PHP_EOL);
//        fwrite($myfile, '进来了');
//        fclose($myfile);

        $post = input('post.');

        if ($post) {
            // 支付成功
            if ($post['trade_status'] == 'TRADE_SUCCESS') {
                $result = BalanceLog::userRechargeBalanceWxPayNotify($post['out_trade_no'], 2);
                if ($result) {
                    return 'success';
                } else {
                    throw new ErrorMessage(['msg' => '充值失败']);
                }
            }
        }


//        fwrite($myfile, $input['trade_status'] . PHP_EOL);
//        fwrite($myfile, '进来了');
//        fclose($myfile);
//        echo '进来了';
    }

    // 支付宝支付回调
    public function aliPayNotify()
    {
        $post = input('post.');
//        $consumptionOrderInfo = Db::table('ijiandian_consumption_order')->where('order',$post['out_trade_no'])->find();
//        return json($consumptionOrderInfo);
        if ($post) {
            // 支付成功
            if ($post['trade_status'] == 'TRADE_SUCCESS') {
                Db::startTrans();
                try {
                    // 预支付订单信息
//                    $consumptionOrderInfo = ConsumptionOrder::where('order', $post['out_trade_no'])->find();
                    $consumptionOrderInfo = Db::table('ijiandian_consumption_order')->where('order',$post['out_trade_no'])->find();
                    if($consumptionOrderInfo){
                        if($consumptionOrderInfo['status']==0){
                            // 优惠券
                            if ($consumptionOrderInfo['coupon_user']!==0) {
                                Db::table('ijiandian_coupon_log')->where('id', $consumptionOrderInfo['coupon_user'])->update(['status' => 1, 'use_time' => time()]);
                            }
                            // 积分
                            if ($consumptionOrderInfo['integral'] > 0) {
                                $integralData = [
                                    'use_action' => 4,
                                    'type' => 2,
                                    'amount' => $consumptionOrderInfo['integral'],
                                    'user' => $consumptionOrderInfo['user'],
                                    'source_type' => 2, // 来源
                                    'remark' => '扫码付款消费',
                                    'mid' => $consumptionOrderInfo['merchant'],
                                    'create_time'=>time(),
                                    'update_time'=>time(),
                                ];
                                Db::name('integral_log')->insert($integralData);
                                Db::table('ijiandian_user')->where('id',$consumptionOrderInfo['user'])->setDec('integral', $consumptionOrderInfo['integral']);
                            }

                            // 余额
                            if ($consumptionOrderInfo['balance'] > 0) {
                                $balanceData = [
                                    'use_action' => 1,
                                    'type' => 2,
                                    'amount' => $consumptionOrderInfo['balance'],
                                    'user' => $consumptionOrderInfo['user'],
                                    'source_type' => 2,
                                    'remark' => '扫码付款消费',
                                    'mid' => $consumptionOrderInfo['merchant'],
                                    'create_time'=>time(),
                                    'update_time'=>time(),
                                ];
                                Db::name('balance_log')->insert($balanceData);
                                Db::table('ijiandian_user')->where('id',$consumptionOrderInfo['user'])->setDec('balance', $consumptionOrderInfo['balance']);
                            }

                            // 返利
                            if ($consumptionOrderInfo['integral_rebate'] > 0) {
                                $integralData = [
                                    'use_action' => 5,
                                    'type' => 1,
                                    'amount' => $consumptionOrderInfo['integral_rebate'],
                                    'user' => $consumptionOrderInfo['user'],
                                    'source_type' => 2,
                                    'remark' => '扫码付款消费返利',
                                    'mid' => $consumptionOrderInfo['merchant'],
                                    'create_time'=>time(),
                                    'update_time'=>time(),
                                ];
                                Db::name('integral_log')->insert($integralData);
                                Db::table('ijiandian_user')->where('id', $consumptionOrderInfo['user'])->setInc('integral', $consumptionOrderInfo['integral_rebate']);
                            }
                            // 修改预支付 订单状态
                            Db::name('consumption_order')->where('order',$post['out_trade_no'])->update(['status'=>1]);

                        }
                    }

                    Db::commit();
                    return 'success';

                } catch (Exception $ex) {
                    Db::rollback();
                    throw new ErrorMessage(['msg'=>'更改状态失败']);
                }

            }
        }

    }

    private function set_xml($args)
    {
        $xml = "<xml>";
        foreach ($args as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    private function return_xml_success($msg)
    {
        $arr = array(
            'return_code' => 'SUCCESS',
            'return_msg' => $msg
        );
        return $this->set_xml($arr);
    }


}
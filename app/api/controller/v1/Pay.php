<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/28
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;


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
        $e = encryptDecrypt(config('qrcode.qrcode_salt'), $mid, 0);
        if (strpos($e,'aoeiu') === false) {
            $e = 0;
        }
        $mid = findNum($e);
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
        throw new SuccessMessage([
            'msg' => '支付失败，请升级APP版本'
        ]);
      
        $user_id = Token::getCurrentTokenUserId();
        $post = input('post.');
        if ($post['total'] <= 0) {
            throw new SuccessMessage(['msg' => '请输入消费总金额']);
        }
        $result = $this->confirmOrderCopy($post, $user_id);
        if ($result) {
            throw new SuccessMessage(['msg' => '支付成功']);
        } else {
            throw new SuccessMessage(['msg' => '支付失败，请重新发起支付']);
        }
    }

    public function confirmOrderCopy($post, $user_id)
    {

        // mid 商家id cid 优惠券id   积分integral 余额balance

        $cids = explode(",", $post['cid']);
        $coupon_log_id = $cids[0];
        $coupon_id = $cids[1];
        $is_integral = $post['is_integral'];
        $is_balance = $post['is_balance'];

        $mid = $post['mid'];
        $merchantInfo = Merchant::get($mid);
        $userInfo = User::get($user_id);


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
            throw new SuccessMessage([
                'msg' => '支付金额不足，还需充值',
                'data' => [
                    'premium' => $total
                ]
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
                        'amount' => $deduct_integral*floatval($merchantInfo['proportion']),
                        'user' => $user_id,
                        'source_type' => 1,
                        'remark' => '扫码付款消费',
                        'mid' => $mid
                    ];
                    IntegralLog::create($integralData);                    
                    ConsumptionOrder::where('order', $order)->update(['integral' => $deduct_integral*floatval($merchantInfo['proportion'])]);                   
                    User::where('id', $user_id)->setDec('integral', $deduct_integral*floatval($merchantInfo['proportion']));
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
                            'source_type' => 1,
                            'remark' => '扫码付款消费返利',
                            'mid' => $mid
                        ];
                        IntegralLog::create($integralData);
                        User::where('id', $user_id)->setInc('integral', $rebateAmount);
                        ConsumptionOrder::where('order', $order)->update(['integral_rebate' => $rebateAmount,'integral_ratio'=>$proportion]);
                    }

                }

                Db::commit();
                return true;

            } catch (Exception $ex) {
                Db::rollback();
                return false;
            }

        }
    }



    private function deductionCouponAmount($coupon_id)
    {
        $coupon_info = Coupon::get($coupon_id);
        if (!empty($coupon_info)) {
            $face_value = $coupon_info['face_value'];
        } else {
            $face_value = 0;
        }
        return floatval($face_value);
    }

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

    private function deductionBalanceAmount($is_balance, $userInfo)
    {
        if ($is_balance == '1') {
            $balance = $userInfo['balance'];
        } else {
            $balance = 0;
        }
        return floatval($balance);
    }

    private function initializationConsumptionOrder($user_id, $total, $merchantInfo)
    {
        $order = getRandomNumber(6) . timeToStr(time(), 'YmdHis') . $user_id;
        ConsumptionOrder::create([
            'user' => $user_id,
            'merchant' => $merchantInfo['id'],
            'merchant_user' => $merchantInfo['user'],
            'total_amount' => $total,
            'order' => $order,
            'merchant_integral_deduction' => $merchantInfo['proportion']
        ]);
        return $order;
    }


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
        $time = date("Y-m-d H:i:s",time());
        $myfile = fopen($time.".txt", "w") or die("Unable to open file!");
        $input = file_get_contents('php://input');

        fwrite($myfile, serialize($input).PHP_EOL);
        fclose($myfile);
      
        $user_id = Token::getCurrentTokenUserId();
        (new ValidAmount())->goCheck();
        $post = input('post.');
        if ($post['type'] != '1' && $post['type'] != '2') {
            throw new ErrorMessage(['msg' => '充值类型错误']);
        }
        // 避免重复充值
        $order = $post['order'];
        $result = BalanceLog::where('order',$order)->find();
        if($result){
            throw new SuccessMessage(['msg' => '充值成功']);
        }
        $result = BalanceLog::userRechargeBalance($post, $user_id);
        if ($result) {
            throw new SuccessMessage(['msg' => '充值成功']);
        }
        throw new ErrorMessage(['msg' => '操作失败，请重新发起充值']);
    }


}
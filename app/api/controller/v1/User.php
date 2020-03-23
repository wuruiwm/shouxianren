<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/23
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\controller\v1;

use app\api\model\Action;
use app\api\model\Merchant;
use app\lib\push\JpushSend;
use app\api\model\CouponLog;
use app\api\model\BalanceLog;
use app\api\model\BookingOrder;
use app\api\model\Comment;
use app\api\model\ConsumptionOrder;
use app\api\model\Like;
use app\api\model\Message;
use app\api\model\Square;
use app\api\model\User as UserModel;
use app\api\service\IhuyiSMS;
use app\api\service\Token;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\Booking;
use app\lib\validate\SimpleReg;
use app\lib\validate\UpdatePassword;
use app\lib\validate\ValidCode;
use app\lib\validate\ValidForgetPassword;
use app\lib\validate\ValidMobile;
use app\lib\validate\ValidPassword;
use app\lib\validate\ValidPayPassword;
use think\Cache;
use think\Db;

class User extends BaseController
{
    protected $beforeActionList = [
        'mustBeGet' => ['only' => ['getrand', 'userread', 'readuserbookingorder', 'readmerchantbookingorder', 'usermembercentre', 'adduserintegral']],
        'mustBePost' => ['only' => ['reg', 'login', 'updateusermobile', 'updateuserpassword', 'updateuser', 'createbookingorder', 'forgetpassword', 'updatepaypassword']],
    ];


    /**
     * @apiDefine  user 用户
     */

    /**
     * @api {get} smscode  1、短信验证码
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  获取短信码
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/smscode?mobile=13013090543
     * @apiParam  {int} mobile 11位手机号
     * @apiName 6
     */
    public function getRand($mobile = '')
    {
        (new ValidMobile())->goCheck();
        $isExist = Cache::get($mobile);
        if ($isExist)
            throw new ErrorMessage([
                'msg' => '请再1分钟后重新获取'
            ]);
        $ihuyi = new IhuyiSMS();
        $result = $ihuyi->sms($mobile);
        if ($result['code'] == 2) {
            throw new SuccessMessage([
                'data' => $result
            ]);
        } else {
            unset($result['sms_code']);
            throw new ErrorMessage([
                'data' => $result
            ]);
        }

    }


    /**
     * @api {post} user/reg  2、注册
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  用户注册
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/reg
     * @apiParam  {int} sex 1男 2女
     * @apiParam  {int} mobile 11位手机号
     * @apiParam  {int} code 6位短信验证码
     * @apiParam  {string} password 6-18位密码
     * @apiParam  {string} repassword 确认密码
     * @apiParam  {int} clause 1同意 2未同意
     * @apiName 7
     */
    public function reg()
    {
        (new SimpleReg())->goCheck();
        $post = input('post.');
        if ($post['clause'] == '2') {
            throw new ErrorMessage([
                'msg' => '注册失败，请先阅读并同意协议'
            ]);
        }
        $mobile = $post['mobile'];
        $code = $post['code'];
        $post['password'] = md5($post['password'] . config('md5.user_password_salt'));
        $post['nickname'] = $post['mobile'];
        $poat['stint'] = '3';
        $cache_code = Cache::get($mobile);
        if (!$cache_code)
            throw new ErrorMessage([
                'msg' => '短信验证码已过期，请重新获取'
            ]);
        if ($code !== $cache_code)
            throw new ErrorMessage([
                'msg' => '验证码错误'
            ]);
        $result = UserModel::createUser($post);
        if ($result)
            throw new SuccessMessage();
    }


    /**
     * @api {post} user/login  3、登录
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  手机号+密码 登录
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/login
     * @apiParam  {int} mobile 11位手机号
     * @apiParam  {string} passwrod 密码
     * @apiName 8
     */
    public function login()
    {
        (new ValidMobile())->goCheck();
        (new ValidPassword())->goCheck();
        $post = input('post.');

        $checkData = [
            'mobile' => $post['mobile'],
            'password' => md5($post['password'] . config('md5.user_password_salt'))
        ];
        $result = UserModel::logCheck($checkData, ['create_time', 'update_time', 'delete_time']);
        if (empty($result))
            throw new ErrorMessage([
                'msg' => '账号或密码错误'
            ]);
        if ($result['status'] == 2) {
            throw new ErrorMessage([
                'msg' => '对不起，您的账号已被限制使用'
            ]);
        }
        $result['user_id'] = $result['id'];
        $key = Token::generateToken();
        $cache_time = config('cache.token_expire');
        $result_cache = cache($key, json_encode($result), $cache_time);
        if (!$result_cache)
            throw new ErrorMessage([
                'msg' => '登录失败'
            ]);
      
        
        $tags = ($result['level_type'] == 1) ? 'user' : 'merchant';

        $res = UserModel::update(['alias' => $result['user_id'], 'tags' => $tags], ['id' => $result['user_id']]);
        if ($res) {
            throw new SuccessMessage([
                'data' => [
                    'token' => $key,
                    'cache_time' => $cache_time,
                    'alias' => $result['user_id'],
                    'tags' => $tags
                ]
            ]);
        }
    }


    /**
     * @api {get} user/read  4、用户信息
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  通过token获取用户信息
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/read
     * @apiParam  {string} token header头部传参
     * @apiName 9
     */
    public function userRead()
    {
        $user_id = Token::getCurrentTokenUserId();
        $user = UserModel::where('id', $user_id)
            ->field(['password', 'pay_pwd', 'create_time', 'update_time', 'delete_time', 'clause'], true)
            ->find();
        $square = Square::where('user',$user_id)->count();// 朋友圈发布信息 待定
        $like = Like::where('user', $user_id)->count();
        $comment = Comment::where('user', $user_id)->count();

        // $coupon_count = Db::name('coupon_log')->where('user', $user_id)
        // ->order('create_time desc')
        // ->where('status', 0)
        // ->count();
        $coupon_data = CouponLog::where('user', $user_id)
        ->order('create_time desc')
        ->where('status', 0)
        ->field(['user', 'use_time', 'delete_time', 'update_time'], true)->select();
        $coupon_count = 0;
        foreach ($coupon_data as $k => $v) {
            if($v['status']['msg'] != '已失效'){
                $coupon_count++;
            }
        }
        $result = [
            'message' => Message::getMessageNotReadList(),
            'square' => $square,
            'like' => $like,
            'comment' => $comment,
            'user' => $user,
            'showRecharge'=>'yes',
            'coupon'=>$coupon_count,
        ];
        throw new SuccessMessage([
            'data' => $result
        ]);
    }

    /**
     * @api {post} user/update/mobile  5、重新绑定手机
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  重新绑定手机
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/update/mobile
     * @apiParam  {string} token header头部传参
     * @apiParam  {string} mobile 新手机号
     * @apiParam  {string} code 验证码
     * @apiName 12
     */
    public function updateUserMobile()
    {
        $user_id = Token::getCurrentTokenUserId();
        (new ValidMobile())->goCheck();
        (new ValidCode())->goCheck();
        $post = input('post.');
        $mobile = $post['mobile'];
        $code = $post['code'];
        $cache_code = Cache::get($mobile);
        if (!$cache_code)
            throw new ErrorMessage([
                'msg' => '短信验证码已过期，请重新获取'
            ]);
        if ($code !== $cache_code)
            throw new ErrorMessage([
                'msg' => '验证码错误'
            ]);
        $result = UserModel::exist('mobile', $mobile);
        if ($result['mobile'] == $mobile)
            throw new ErrorMessage([
                'msg' => '绑定失败，该手机已经存在'
            ]);
        $result = UserModel::updateUser($user_id, $post);
        if ($result)
            throw new SuccessMessage();
        throw new ErrorMessage();
    }

    /**
     * @api {post} user/update/password  6、重新修改密码
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  重新修改密码
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/update/password
     * @apiParam  {string} token header头部传参
     * @apiParam  {string} old_password 旧密码
     * @apiParam  {string} password 新密码
     * @apiParam  {string} repassword 确认密码
     * @apiName 13
     */
    public function updateUserPassword()
    {
        $user_id = Token::getCurrentTokenUserId();
        (new UpdatePassword())->goCheck();
        $post = input('post.');
        $old_password = md5($post['old_password'] . config('md5.user_password_salt'));
        $result = Token::getCurrentTokenUser();
        if ($old_password !== $result['password'])
            throw new ErrorMessage(['msg' => '原始密码错误']);
        $post['password'] = md5($post['password'] . config('md5.user_password_salt'));
        $result = UserModel::updateUser($user_id, $post);
        if ($result) {
            $token = \think\Request::instance()->header('token');
            Cache::rm($token);
            throw new SuccessMessage();
        }
        throw new ErrorMessage();
    }

    /**
     * @api {post} user/update/user  7、修改资料
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  修改资料
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/update/user
     * @apiParam  {string} token header头部传参
     * @apiParam  {string} [avatar] 头像 (上传图片成功后的ID)
     * @apiParam  {string} [nickname] 昵称
     * @apiParam  {string} [birthday] 生日 格式 0000-00-00
     * @apiParam  {string} [email] 邮箱
     * @apiName 14
     */
    public function updateUser()
    {
        $post = input('post.');
        $user_id = Token::getCurrentTokenUserId();
        if(isset($post['integral'])){
            throw new ErrorMessage(['msg'=>'去你MD想干嘛']);
        }
        if(isset($post['balance'])){
            throw new ErrorMessage(['msg'=>'去你MD想干嘛']);
        }
        $result = UserModel::updateUser($user_id, $post);
        if ($result)
            throw new SuccessMessage();
        throw new ErrorMessage();
    }

    /**
     * @api {post} user/booking  8、我要预订
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  活动详情页，我要预订
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/booking
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} merchant 商户ID
     * @apiParam  {int} merchant_user 商户用户ID
     * @apiParam  {int} action 活动ID
     * @apiParam  {string} name 预订人姓名
     * @apiParam  {string} mobile 预订人手机号
     * @apiName 23
     */
    public function createBookingOrder()
    {
        $user_id = Token::getCurrentTokenUserId();
        $post = input('post.');
        $post['user'] = $user_id;

        (new Booking())->goCheck();
        $result = BookingOrder::createBookingOrder($post);
        if ($result){            
          	$action_id = $post['action'];
            $action =Action::get($action_id);
            $push_data = [
                'title' => $action['title'],
                'content' => '您有新的订单，请及时处理'
            ];
            $push_param = [
                'path' => 'merchant_order',
                'id' => ''
            ];
            $jpush = new JpushSend();
            $res = $jpush->send_pub([$post['merchant_user']], $push_data, $push_param);



            $merchantInfo = Merchant::get($post['merchant']);

            $data = [
                'user'=>$post['name'],
                'title'=>$action['title'],
                'time'=>timeToStr(time()),
                'mobile'=>$merchantInfo['receive_mobile'],
                'user_mobile'=>$post['mobile'],
            ];
            $sms = new IhuyiSMS();
            $result = $sms->smsNotice($data);
            throw new SuccessMessage();
        }
        throw new ErrorMessage();
    }

    /**
     * @api {post} user/forget/password  9、忘记密码
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  用户登录时，忘记密码
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/forget/password
     * @apiParam  {string} mobile 登录账号(手机号)
     * @apiParam  {int} code 验证码
     * @apiParam  {string} password 新密码
     * @apiParam  {string} repassword 确认新密码
     * @apiName 24
     */
    public function forgetPassword()
    {
        (new ValidForgetPassword())->goCheck();
        $post = input('post.');
        $cache_code = Cache::get($post['mobile']);
        if (!$cache_code)
            throw new ErrorMessage([
                'msg' => '短信验证码已过期，请重新获取'
            ]);
        if ($post['code'] !== $cache_code)
            throw new ErrorMessage([
                'msg' => '验证码错误'
            ]);
        $result = UserModel::exist('mobile', $post['mobile']);
        if (!$result)
            throw new ErrorMessage([
                'msg' => '账号不存在，请先注册'
            ]);
        $data['password'] = md5($post['password'] . config('md5.user_password_salt'));
        $result = UserModel::updateUser($result['id'], $data);
        if (!$result)
            throw new ErrorMessage(['msg' => '操作失败，请稍后重试']);
        throw new SuccessMessage();
    }

    /**
     * @api {get} user/booking/user/order  10、我的订单
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  我的订单
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/booking/user/order
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiName 25
     * @apiSuccess {json} data 订单列表
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     * {
     * "msg": "success",
     * "code": 20000,
     * "data": {
     * "id": 1,
     * "merchant": "寿州国际酒店",
     * "action": {
     * "id": 1,
     * "title": "高级双床房(【展业国际官方直营】正品直销价+免押金入住+优选房间+发票由酒店开具 无早)",
     * "img": "http://sxr.ijiandian.com//uploads/admin/20190524/d6764807c448e6ffe16844486eb30285.jpg"
     * },
     * "status": 0, // 0待处理，1已处理
     * "create_time": "2019-05-25 10:31"
     * },
     * "requestUrl": "http://sxr.ijiandian.com/api/v1/user/booking/user/order"
     * }
     */
    public function readUserBookingOrder()
    {
        $user_id = Token::getCurrentTokenUserId();
        $result = BookingOrder::where('user', $user_id)->order('create_time desc')->select();
        if (!$result)
            throw new SuccessMessage([
                'msg' => '空空如也'
            ]);
        throw new SuccessMessage([
            'data' => $result
        ]);
    }


    /**
     * @api {get} user/booking/merchant/order  11、商家中心
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  商家订单
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/booking/merchant/order
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiName 26
     * @apiSuccess {json} data 订单列表
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     * {
     * "msg": "success",
     * "code": 20000,
     * "data": {
     * "id": 1,
     * "user": {
     * "avatar": "http://sxr.ijiandian.com\\uploads\\user\\20190522\\afeed961b4986476a8a35989f3725ff6.jpg",
     * "nickname": "iActing"
     * },
     * "action": {
     * "id": 1,
     * "title": "高级双床房(【展业国际官方直营】正品直销价+免押金入住+优选房间+发票由酒店开具 无早)",
     * "img": "http://sxr.ijiandian.com//uploads/admin/20190524/d6764807c448e6ffe16844486eb30285.jpg"
     * },
     * "status": 0, // 0待处理，1已处理
     * "name": "刘先生",
     * "mobile": "13013013013",
     * "create_time": "2019-05-25 10:31"
     * },
     * "requestUrl": "http://sxr.ijiandian.com/api/v1/user/booking/merchant/order"
     * }
     */
    public function readMerchantBookingOrder()
    {
        $user_id = Token::getCurrentTokenUserId();
        $result = BookingOrder::where('merchant_user', $user_id)->order('create_time desc')->select();
        if (!$result)
            throw new SuccessMessage([
                'msg' => '空空如也'
            ]);
        throw new SuccessMessage([
            'data' => $result
        ]);
    }


    /**
     * @api {get} user/membercentre  12、我的会员
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  个人中心，我的会员
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/membercentre
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiName 30
     * @apiSuccess {json} data 会员信息
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     * {
     * "msg": "success",
     * "code": 20000,
     * "data": {
     * "check_in": { // 签到信息
     * "status": "0",// 0 未签到，1已签到
     * "msg": "签到",
     * "number": "1"// 签到积分
     * },
     * "share": {// 分享信息
     * "status": "0",
     * "msg": "分享",
     * "number": "1"
     * },
     * "user": {
     * "balance_total": 0,// 累计充值 数
     * "integral": 50,// 积分数
     * "balance": 5,// 余额数
     * "nickname": "iActing",
     * "avatar": {
     * "id": 73,
     * "filepath": "http://sxr.ijiandian.com\\uploads\\user\\20190522\\afeed961b4986476a8a35989f3725ff6.jpg"
     * }
     * }
     * },
     * "requestUrl": "http://sxr.ijiandian.com/api/v1/user/membercentre"
     * }
     */
    public function userMemberCentre()
    {
        $user_id = Token::getCurrentTokenUserId();
        $result = UserModel::userMemberCentre($user_id);
        throw new SuccessMessage([
            'data' => $result
        ]);
    }


    /**
     * @api {get} user/add/integral  13、签到/分享
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription 用户会员中心签到，或分享文章获得 1积分
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/add/integral
     * @apiParam  {string} token 将Token添加到请求头Header中
     * @apiParam  {int} action 1签到，2分享
     * @apiName 31
     */
    public function addUserIntegral($action = '')
    {

        if (empty($action) || $action < 0 || $action > 2)
            throw new ErrorMessage(['msg' => 'action参数取值范围1,2']);
        $user_id = Token::getCurrentTokenUserId();
        $result = UserModel::addUserIntegral($user_id, $action);
        if ($result)
            throw new SuccessMessage();

    }


    /**
     * @api {post} user/update/pay_password  14、修改支付密码
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  设置中，修改支付密码
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/update/pay_password
     * @apiParam  {string} mobile 登录账号(手机号)
     * @apiParam  {int} code 验证码
     * @apiParam  {string} password 新密码
     * @apiParam  {string} repassword 确认新密码
     * @apiName 34
     */
    public function updatePayPassword()
    {
        (new ValidPayPassword())->goCheck();
        $post = input('post.');
        $cache_code = Cache::get($post['mobile']);
        if (!$cache_code)
            throw new ErrorMessage([
                'msg' => '短信验证码已过期，请重新获取'
            ]);
        if ($post['code'] !== $cache_code)
            throw new ErrorMessage([
                'msg' => '验证码错误'
            ]);
        $result = UserModel::exist('mobile', $post['mobile']);
        if (!$result)
            throw new ErrorMessage([
                'msg' => '账户不存在'
            ]);
        $data['pay_pwd'] = md5($post['password'] . config('md5.user_pay_password_salt'));
        $result = UserModel::updateUser($result['id'], $data);
        if (!$result)
            throw new ErrorMessage(['msg' => '操作失败，请稍后重试']);
        throw new SuccessMessage();
    }

    /**
     * @api {get} user/booking/merchant/check  15、商家审核
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  商家订单页面点击审核调此接口，并弹出提示框，是否已确认
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/booking/merchant/check
     * @apiParam  {int} id
     * @apiName 43
     */
    public function bookingCheck($id='')
    {
        if(empty(trim($id)))
            throw new ErrorMessage(['msg'=>'必填参数不能为空']);

       $result = BookingOrder::where('id', $id)
            ->update(['status' => 1,'handle_time'=>time()]);
      
        
        if($result){
            $booking_order = BookingOrder::get($id);
          
            $action_id = $booking_order['action']['id'];
            $action =Action::get($action_id);
            $push_data = [
                'title' => $action['title'],
                'content' => '您的预订单处理成功，祝您生活愉快！'
            ];
          
            $push_param = [
                'path' => 'user_order',
                'id' => ''
            ];
            $test = new JpushSend();
            $res = $test->send_pub([$booking_order['user']['id']], $push_data, $push_param);
          
              return $res;
            throw new SuccessMessage();
        }
            
        throw new ErrorMessage();
    }

    /**
     * @api {get} user/payment/order  16、用户付款记录
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  用户订单，到店付款消费记录
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/payment/order
     * @apiParam  {string} token
     * @apiName 44
     * @apiSuccess {json} data 消费记录
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     [
    {
    "id": 2,
    "user": {
    "avatar": {
    "id": 73,
    "filepath": "http://sxr.ijiandian.com\\uploads\\user\\20190522\\afeed961b4986476a8a35989f3725ff6.jpg"
    },
    "nickname": "iActing"
    },
    "merchant": { // 商家信息(消费哪家店)，自己可选择的进行筛选显示
    "id": 1,
    "title": "寿州国际酒店",
    "img": {
    "id": 71,
    "url": "http://sxr.ijiandian.com/uploads/admin/20190518/a5b77cd7003dfca59e0700c4078ace1e.jpg"
    },
    "qrcode_url": "uploads/admin/qrcode/YFWl92l7a5XEX8D6vIDGEiO0O0O2.png",
    "type": 3,
    "user": 1,
    "integral": 10,
    "proportion": 10,
    "address": "寿县寿春镇南门大圆盘，近明珠大道和下关交叉口"
    },
    "merchant_user": 1,
    "coupon": { // 消费的优惠券信息，值或空
    "id": 12,
    "title": "50元优惠券",
    "face_value": "50.00"
    },
    "coupon_user": 3,
    "integral": "4.82",// 消费积分，建议取下边的抵扣积分数进行展示说明
    "merchant_integral_deduction": 10, // 表示10积分==1元
    "balance": "49.52",// 消费余额
    "total_amount": "100.00", // 总消费金额
    "real_payment": "0.00",
    "order": "615660201906101814461",
    "create_time": "2019-06-10 18:14:46",
    "merchant_integral_deduction_copy": "0.48" //消费积分所抵扣的人民币额度
    },
     ]
     */

    public function paymentOrder()
    {
        $user_id = Token::getCurrentTokenUserId();
        $result = ConsumptionOrder::getListByUser($user_id);
        foreach ($result as $k => $v) {
            $result[$k] = $v->toArray();
        }
        foreach($result as $k=>$v){
            if($v['merchant_integral_deduction']!==0){
                $integral =  $v['integral'] / $v['merchant_integral_deduction'];
                $result[$k]['merchant_integral_deduction_copy'] = number_format($integral,2,'.','');
            }
            if(empty($v['merchant'])){
                $result[$k]['merchant'] = ['title'=>'卡券包购买'];
            }
            //小程序用
            $result[$k]['create_time_new'] = date("Y-m-d",strtotime($v['create_time']));
        }
        if($result){
            throw new SuccessMessage(['data'=>$result]);
        }
        throw new SuccessMessage(['msg'=>'暂无相关信息']);
    }

    /**
     * @api {get} user/recharge/order  17、用户充值记录
     * @apiGroup user
     * @apiVersion 0.1.0
     * @apiDescription  用户充值记录
     * @apiSampleRequest http://sxr.ijiandian.com/api/v1/user/recharge/order
     * @apiParam  {string} token
     * @apiName 45
     */
    public function rechargeOrder()
    {
        $user_id = Token::getCurrentTokenUserId();
        $result =  BalanceLog::getListByUser($user_id);
        if($result){
            throw new SuccessMessage(['data'=>$result]);
        }
        throw new SuccessMessage(['msg'=>'暂无相关信息']);
    }
}
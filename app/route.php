<?php
/*
 * @Author: 傍晚升起的太阳
 * @QQ: 1250201168
 * @Email: wuruiwm@qq.com
 * @Date: 2019-11-21 14:08:32
 * @LastEditors: 傍晚升起的太阳
 * @LastEditTime: 2019-12-03 14:23:21
 */
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://tplay.pengyichen.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------


use think\Route;

//url美化 例：Route::rule('blog/:id','index/blog/read');


//---------------------------------后端API接口大全---------------------------------------------------

Route::any('adminapi/qiniu/save', 'admin/Qiniuconfig/updateQiniuconfig');
Route::any('adminapi/qiniu/read', 'admin/Qiniuconfig/readQiniuconfig');
Route::any('adminapi/rechargeconfig/save', 'admin/Rechargeconfig/updateRechargeconfig');
Route::any('adminapi/rechargeconfig/read', 'admin/Rechargeconfig/readRechargeconfig');
Route::any('adminapi/upload', 'admin/Common/adminUpload');
Route::any('adminapi/upload/video', 'admin/Upload/uploadVideo');
Route::any('adminapi/banner/create', 'admin/Banner/createBanner');
Route::any('adminapi/banner/delete', 'admin/Banner/deleteBanner');
Route::any('adminapi/banner/update', 'admin/Banner/updateBanner');
Route::any('adminapi/banner/read', 'admin/Banner/readBanner');
Route::any('adminapi/banner/read_id', 'admin/Banner/readBannerById');
Route::any('adminapi/newscate/create', 'admin/NewsCate/createNewsCate');
Route::any('adminapi/newscate/delete', 'admin/NewsCate/deleteNewsCate');
Route::any('adminapi/newscate/update', 'admin/NewsCate/updateNewsCate');
Route::any('adminapi/newscate/read', 'admin/NewsCate/readNewsCate');
Route::any('adminapi/news/create', 'admin/News/createNews');
Route::any('adminapi/news/delete', 'admin/News/deleteNews');
Route::any('adminapi/news/update', 'admin/News/updateNews');
Route::any('adminapi/news/read', 'admin/News/readNews');
Route::any('adminapi/news/read_id', 'admin/News/readNewsById');
Route::any('api/:version/news/read/independent/id', 'api/:version.News/readIndependentNewsById');
Route::any('adminapi/news/read/comment/list', 'admin/News/readListByIdComment');
Route::any('adminapi/news/comment/del', 'admin/News/deleteNewsCommentByIds');
Route::any('adminapi/user/update', 'admin/User/updateUser');
Route::any('adminapi/user/read', 'admin/User/readUser');
Route::any('adminapi/user/read_id', 'admin/User/readUserById');
Route::any('adminapi/user/rechargeintegral', 'admin/User/rechargeIntegral');
Route::any('adminapi/user/rechargebalance', 'admin/User/rechargeBalance');
Route::any('adminapi/user/rebateintegral', 'admin/User/rebateIntegral');
Route::any('adminapi/user/rebatebalance', 'admin/User/rebateBalance');
Route::any('adminapi/merchant/update', 'admin/Merchant/updateMerchant');
Route::any('adminapi/merchant/read', 'admin/Merchant/readMerchant');
Route::any('adminapi/merchant/read_id', 'admin/Merchant/readMerchantById');
Route::any('adminapi/merchantcate/create', 'admin/MerchantCate/createMerchantCate');
Route::any('adminapi/merchantcate/delete', 'admin/MerchantCate/deleteMerchantCate');
Route::any('adminapi/merchantcate/update', 'admin/MerchantCate/updateMerchantCate');
Route::any('adminapi/merchantcate/read', 'admin/MerchantCate/readMerchantCate');
Route::any('adminapi/about/us/read', 'admin/AboutUs/readAboutUs');
Route::any('adminapi/about/us/save', 'admin/AboutUs/updateAboutUs');
Route::any('adminapi/about/cooperation/read', 'admin/AboutCooperation/readAboutCooperation');
Route::any('adminapi/about/cooperation/save', 'admin/AboutCooperation/updateAboutCooperation');
Route::any('adminapi/actioncate/create', 'admin/ActionCate/createActionCate');
Route::any('adminapi/actioncate/delete', 'admin/ActionCate/deleteActionCate');
Route::any('adminapi/actioncate/update', 'admin/ActionCate/updateActionCate');
Route::any('adminapi/actioncate/read', 'admin/ActionCate/readActionCate');
Route::any('adminapi/action/create', 'admin/Action/createAction');
Route::any('adminapi/action/delete', 'admin/Action/deleteAction');
Route::any('adminapi/action/update', 'admin/Action/updateAction');
Route::any('adminapi/action/read', 'admin/Action/readAction');
Route::any('adminapi/action/read_id', 'admin/Action/readActionById');
Route::any('adminapi/action/read/comment/list', 'admin/Action/readListByIdComment');
Route::any('adminapi/action/comment/del', 'admin/Action/deleteActionCommentByIds');
Route::any('adminapi/bookingorder/read', 'admin/BookingOrder/readBookingOrder');
Route::any('adminapi/coupon/create', 'admin/Coupon/createCoupon');
Route::any('adminapi/coupon/get/list', 'admin/Coupon/getList');
Route::any('adminapi/coupon/get/id', 'admin/Coupon/getListById');
Route::any('adminapi/coupon/update/id', 'admin/Coupon/updateById');
Route::any('adminapi/coupon/data/export', 'admin/Coupon/dataExport');
Route::any('adminapi/message/create', 'admin/Message/createMessage');
Route::any('adminapi/message/delete', 'admin/Message/deleteMessage');
Route::any('adminapi/message/update', 'admin/Message/updateMessage');
Route::any('adminapi/message/read', 'admin/Message/readMessage');
Route::any('adminapi/message/read_id', 'admin/Message/readMessageById');
Route::any('adminapi/square/read', 'admin/Square/readContents');
Route::any('adminapi/merchant/qrcode', 'admin/merchant/qrcode');
Route::any('adminapi/square/read/list', 'admin/Square/readList');
Route::any('adminapi/square/read/comment/list', 'admin/Square/readListByIdComment');
Route::any('adminapi/square/comment/del', 'admin/Square/deleteSquareCommentByIds');
Route::any('adminapi/square/del', 'admin/Square/deleteSquareByIds');
Route::any('adminapi/consumptionorder/get/list', 'admin/ConsumptionOrder/getList');
Route::any('adminapi/consumptionorder/data/export', 'admin/ConsumptionOrder/dataExport');
Route::any('adminapi/consumptionorder/data/exportaide', 'admin/ConsumptionOrder/dataExportAide');
Route::any('adminapi/balancelog/get/list', 'admin/BalanceLog/getList');
Route::any('adminapi/balancelog/data/export', 'admin/BalanceLog/dataExport');
Route::any('adminapi/integrallog/get/list', 'admin/IntegralLog/getList');
Route::any('adminapi/integrallog/data/export', 'admin/IntegralLog/dataExport');

Route::any('adminapi/jpush', 'admin/JPusha/jpush');
Route::any('adminapi/update/jpush/status', 'admin/JPusha/updateJpushStatus');


//---------------------------------前端API接口大全---------------------------------------------------
Route::any('api/:version/upload/image', 'api/:version.Upload/userUpload');
Route::any('api/:version/upload/image/base64', 'api/:version.Upload/userUploadBase64');
Route::any('api/:version/upload/image/qiniu', 'api/:version.Upload/squareImgUpload');
Route::any('api/:version/banner/read', 'api/:version.Banner/readBanner');
Route::any('api/:version/news/read', 'api/:version.News/readNews');
Route::any('api/:version/news/read/id', 'api/:version.News/readNewsById');
Route::any('api/:version/news/search', 'api/:version.News/newSearch');
Route::any('api/:version/newscate/read', 'api/:version.NewsCate/readNewsCate');
Route::any('api/:version/smscode', 'api/:version.User/getRand');
Route::any('api/:version/user/reg', 'api/:version.User/reg');
Route::any('api/:version/user/login', 'api/:version.User/login');
Route::any('api/:version/user/read', 'api/:version.User/userRead');
Route::any('api/:version/user/update/mobile', 'api/:version.User/updateUserMobile');
Route::any('api/:version/user/update/password', 'api/:version.User/updateUserPassword');
Route::any('api/:version/user/update/user', 'api/:version.User/updateUser');
Route::any('api/:version/user/booking', 'api/:version.User/createBookingOrder');
Route::any('api/:version/user/forget/password', 'api/:version.User/forgetPassword');
Route::any('api/:version/user/booking/user/order', 'api/:version.User/readUserBookingOrder');
Route::any('api/:version/user/booking/merchant/order', 'api/:version.User/readMerchantBookingOrder');
Route::any('api/:version/user/membercentre', 'api/:version.User/userMemberCentre');
Route::any('api/:version/user/add/integral', 'api/:version.User/addUserIntegral');
Route::any('api/:version/user/update/pay_password', 'api/:version.User/updatePayPassword');
Route::any('api/:version/user/booking/merchant/check', 'api/:version.User/bookingCheck');
Route::any('api/:version/user/payment/order', 'api/:version.User/paymentOrder');
Route::any('api/:version/user/recharge/order', 'api/:version.User/rechargeOrder');
Route::any('api/:version/comment/create', 'api/:version.Comment/createComment');
Route::any('api/:version/like/okorno', 'api/:version.Like/createLike');
Route::any('api/:version/about/us', 'api/:version.About/readAboutUs');
Route::any('api/:version/about/cooperation', 'api/:version.About/readAboutCooperation');
Route::any('api/:version/actioncate/read', 'api/:version.ActionCate/readActionCate');
Route::any('api/:version/action/read', 'api/:version.Action/readAction');
Route::any('api/:version/action/read/id', 'api/:version.Action/readActionById');
Route::any('api/:version/coupon/read', 'api/:version.Coupon/readCoupon');
Route::any('api/:version/coupon/receive', 'api/:version.Coupon/receiveCoupon');
Route::any('api/:version/coupon/user', 'api/:version.Coupon/userCoupon');
Route::any('api/:version/pay/offline', 'api/:version.Pay/offlinePay');
Route::any('api/:version/pay/select/coupon', 'api/:version.Pay/selectCoupon');
Route::any('api/:version/pay/password', 'api/:version.Pay/payPassword');
Route::any('api/:version/pay/confirmorder', 'api/:version.Pay/confirmOrder');
Route::any('api/:version/pay/user/recharge', 'api/:version.Pay/userRechargeBalance');

// 微信充值 支付相关
Route::any('api/:version/pay/wx/recharge/notify', 'api/:version.Pay/wxPayRechargeNotify');// 充值回调
Route::any('api/:version/pay/wx/pay/notify', 'api/:version.Pay/wxPayNotify');//支付回调
Route::any('api/:version/pre/pay', 'api/:version.Pay/rechargePre'); // 预充值 订单
Route::any('api/:version/pay/status', 'api/:version.Pay/getOrderStatus');// 微信订单查询
// 支付宝充值 支付相关
Route::any('api/:version/pay/ali/recharge/notify', 'api/:version.Pay/aliPayRechargeNotify');// 充值回调
Route::any('api/:version/pay/ali/pay/notify', 'api/:version.Pay/aliPayNotify');//支付回调


Route::any('api/:version/message/list', 'api/:version.Message/getMessageList');
Route::any('api/:version/message/read/id', 'api/:version.Message/getMessage');
Route::any('api/:version/square/read', 'api/:version.Square/readList');
Route::any('api/:version/square/publish', 'api/:version.Square/publish');
Route::any('api/:version/merchant/order', 'api/:version.Merchant/order');


Route::any('api/v1/qiniutoken', 'admin/Upload/qiniuToken'); // 七牛token



//前端new Author@傍晚升起的太阳 email:wuruiwm@qq.com
Route::any('api/:version/is_sweepcode', 'api/:version.Sweepcode/is_sweepcode');
Route::any('api/:version/hotel/list', 'api/:version.hotel/list');
Route::any('api/:version/hotelorder/add', 'api/:version.hotelorder/add');
Route::any('api/:version/hotelorder/list', 'api/:version.hotelorder/list');
Route::any('api/:version/hotelorder/detail', 'api/:version.hotelorder/detail');
Route::any('api/:version/hotelorder/bindorder', 'api/:version.hotelorder/bindorder');
Route::any('api/:version/hotelorder/cancel', 'api/:version.hotelorder/cancel');
Route::rule('api/v1/wx/login','api/v1.wx/login');
Route::rule('api/v1/wx/userdecrypt','api/v1.wx/userdecrypt');
Route::rule('api/v1/wx/mobiledecrypt','api/v1.wx/mobiledecrypt');
Route::rule('api/v1/integral','api/v1.integral/list');
Route::rule('api/v1/coupon/bag','api/v1.coupon/bag');
Route::rule('api/v1/coupon/bag/pay','api/v1.coupon/bag_pay');
Route::any('api/:version/pay/wx/bag/notify', 'api/:version.Pay/wxbagNotify');
Route::any('api/:version/pay/ali/bag/notify', 'api/:version.Pay/alibagNotify');
Route::rule('api/v1/coupon/bag/pay/status','api/v1.coupon/bag_pay_status');










$url = \think\Db::name("urlconfig")->where(['status' => 1])->column('aliases,url');
foreach ($url as $k => $val) {
	\think\Route::rule($k,$val);
}


return [
    '__pattern__' => [
        'name' => '\w+',
    ],
    '[hello]'     => [
        ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
        ':name' => ['index/hello', ['method' => 'post']],
    ],

];



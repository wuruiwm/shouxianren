<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/15
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\service;


use app\lib\exception\ErrorMessage;
use think\Cache;

class IhuyiSMS
{
    public function sms($mobile)
    {
        $randNum = getRandomNumber(6);
        $account = 'C92068738';
        $apikey = '2093f611944a667f069d669c821a19ec';
        $mobile = $mobile;
        $content = '您的验证码是：'.$randNum.'。请不要把验证码泄露给其他人。';
        $time = time();
        $password=md5($account.$apikey.$mobile.$content.$time);
        $format = 'json';

        $data = [
            'account'=>$account,
            'password'=>$password,
            'mobile'=>$mobile,
            'content'=>$content,
            'time'=>$time,
            'format'=>$format
        ];

        $url = 'http://106.ihuyi.com/webservice/sms.php?method=Submit';
        $result = http_request($url,$data);
        $result = json_decode($result,true);
        $result['mobile'] = $mobile;
        $result['sms_code'] = $randNum;
        $result_cache = Cache::set($mobile,$randNum,60);
        if(!$result_cache)
            throw new ErrorMessage([
                'msg'=>'smsCode缓存失败'
            ]);

        return $result;
    }
  
  	public function smsNotice($data)
    {
        $account = 'C92068738';
        $apikey = '2093f611944a667f069d669c821a19ec';
        $mobile = $data['mobile'];
        $content = '您有新的预订信息：'.$data['user'].'在'.$data['time'].'预订了'.$data['title'].'，联系号码为'.$data['user_mobile'].'。请您及时与其联系。寿县人移动客户端智能推送';
        $time = time();
        $password=md5($account.$apikey.$mobile.$content.$time);
        $format = 'json';

        $data = [
            'account'=>$account,
            'password'=>$password,
            'mobile'=>$mobile,
            'content'=>$content,
            'time'=>$time,
            'format'=>$format
        ];

        $url = 'http://106.ihuyi.com/webservice/sms.php?method=Submit';
        $result = http_request($url,$data);
        $result = json_decode($result,true);
        return $result;
    }




}
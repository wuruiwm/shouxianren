<?php
/*
 * @Author: 傍晚升起的太阳
 * @QQ: 1250201168
 * @Email: wuruiwm@qq.com
 * @Date: 2019-11-22 09:19:51
 * @LastEditors: 傍晚升起的太阳
 * @LastEditTime: 2019-12-05 09:34:52
 */
namespace app\api\controller\v1;

use think\Db;
use app\api\service\Token;
use app\api\model\User as UserModel;

class Wx extends BaseController
{
    public function login(){
        $code = input('code');
        if(empty($code)){
            msg(0,'code不能为空');
        }
 		$param['appid'] = config('wx.appid');    //小程序id
 		$param['secret'] = config('wx.appsecret');    //小程序密钥
 		$param['js_code'] = define_str_replace($code);
 		$param['grant_type'] = 'authorization_code';
 		$http_key = httpCurl('https://api.weixin.qq.com/sns/jscode2session', $param, 'GET');
        $session_key = json_decode($http_key,true);
        if (!empty($session_key['session_key'])) {
            return ['status'=>1,'data'=>$session_key];
        }else{
            msg(0,'获取session_key失败');
        }
    }
    public function userdecrypt(){
        $user = $this->decrypt();
        if(!empty($user) && !empty($user['openId'])){
            showjson(['status'=>1,'data'=>$user]);
        }else{
            msg(0,'获取用户信息失败'.json_encode($user,JSON_UNESCAPED_UNICODE));
        }
    }
    public function mobiledecrypt(){
        $mobile_res = $this->decrypt();
        //$mobile_res = json_decode('{"phoneNumber":"17355105312","purePhoneNumber":"17355105312","countryCode":"86","watermark":{"timestamp":1574395184,"appid":"wx87b9118427e1082c"}}',true);
        if(empty($mobile_res) || empty($mobile_res['purePhoneNumber'])){
            msg(0,'获取用户手机号失败');
        }
        $gender = input('gender');
        if($gender != 1 && $gender != 2){
            msg(0,'请传入正确的性别');
        }
        $user_res = Db::name('user')->where('mobile',$mobile_res['purePhoneNumber'])->find();
        if(empty($user_res)){
            $data['mobile'] = $mobile_res['purePhoneNumber'];
            $data['password'] = md5(getRandomChar(16). config('md5.user_password_salt'));
            $data['nickname'] = $mobile_res['purePhoneNumber'];
            $data['stint'] = '3';
            $data['sex'] = $gender;
            $data['clause'] = 1;
            $data['avatar'] = 4;
            UserModel::createUser($data);
            $user_res = Db::name('user')->where('mobile',$mobile_res['purePhoneNumber'])->find();
        }
        if($user_res['status'] == 2){
            msg(0,'对不起，您的账号已被限制使用');
        }
        $user_res['user_id'] = $user_res['id'];
        $key = Token::generateToken();
        $cache_time = config('cache.token_expire');
        $result_cache = cache($key, json_encode($user_res), $cache_time);
        if(!$result_cache){
            msg(0,'登录失败');
        }
        $tags = ($user_res['level_type'] == 1) ? 'user' : 'merchant';
        $res = UserModel::update(['alias' => $user_res['user_id'], 'tags' => $tags], ['id' => $user_res['user_id']]);
        if($res){
            showjson([
                'status'=>1,
                'data' => [
                    'token' => $key,
                    'cache_time' => $cache_time,
                    'alias' => $user_res['user_id'],
                    'tags' => $tags
                ]
            ]);
        }else{
            msg(0,'登录失败,请重试'); 
        }
    }
    protected function decrypt(){
        $session_key = input('session_key');
        $encrypteData = input('encrypteData');
        $iv = input('iv');
        if(empty($session_key)){
            msg(0,'session_key不能为空');
        }
        if(empty($encrypteData)){
            msg(0,'encrypteData不能为空');
        }
        if(empty($iv)){
            msg(0,'iv不能为空');
        }
        $param['appid'] = config('wx.appid');
        $appid = $param['appid'];
        $encrypteData = urldecode($encrypteData);
        $encrypteData = define_str_replace($encrypteData);
        $iv = define_str_replace($iv);
        $errCode = decryptData($appid, $session_key, $encrypteData, $iv);
        $res = $errCode;
        return $res;
    }
}
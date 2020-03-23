<?php
/*
 * @Author: 傍晚升起的太阳
 * @QQ: 1250201168
 * @Email: wuruiwm@qq.com
 * @Date: 2019-11-22 09:19:51
 * @LastEditors: 傍晚升起的太阳
 * @LastEditTime: 2019-11-26 16:54:58
 */
namespace app\api\controller\v1;

use think\Db;
use app\api\service\Token;

class Integral extends BaseController
{
    public function list(){
        $user_id = Token::getCurrentTokenUserId();
        $data = Db::name('integral_log')->field(['use_action','type','amount','create_time'])->where('user',$user_id)->order('id desc')->select();
        foreach ($data as $k => $v) {
            if($v['use_action'] == 0){
                $data[$k]['use_action'] = '未知';
            }else if($v['use_action'] == 1){
                $data[$k]['use_action'] = '签到';
            }else if($v['use_action'] == 2){
                $data[$k]['use_action'] = '分享';
            }else if($v['use_action'] == 3){
                $data[$k]['use_action'] = '充值';
            }else if($v['use_action'] == 4){
                $data[$k]['use_action'] = '消费';
            }else if($v['use_action'] == 5){
                $data[$k]['use_action'] = '返还';
            }else if($v['use_action'] == 6){
                $data[$k]['use_action'] = '兑换券';
            }
            if($v['type'] == 1){
                $data[$k]['amount'] = '+'.$v['amount'];
            }else{
                $data[$k]['amount'] = '-'.$v['amount'];
            }
            unset($data[$k]['type']);
            $data[$k]['create_time'] = date('Y-m-d H:i',$v['create_time']);
        }
        if($data){
            showjson(['status'=>1,'data'=>$data]);
        }else{
            showjson(['status'=>0,'data'=>[],'msg'=>'列表数据为空']);
        }
    }
}
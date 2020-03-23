<?php
namespace app\api\controller\v1;

use think\Db;
class Hotel extends BaseController
{
    public function list(){
        $actionid = input('id');
        $arr = Db::name('hotel')->field(['id','name','num','price','head_img'])->where('actionid',$actionid)->select();
        foreach ($arr as $k => $v) {
        	$arr[$k]['head_img'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]".$v['head_img'];
        }
        if (!empty($arr)) {
            return ['status'=>1,'list'=>$arr];
        }else{
            return ['status'=>0,'list'=>[]];
        }
    }
}
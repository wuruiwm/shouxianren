<?php
namespace app\admin\controller;

use think\Db;

class Hotel extends Permissions{
    public function index(){
        $data = Db::name('action')->field(['id',"title"])->where('action_cate','3')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function list(){
      $page = input('page');
      $limit = input('limit');
      if (empty($page) || !is_numeric($page)) {
        return ['status'=>0,'msg'=>'请输入正确的页码'];
      }
      if (empty($limit) || !is_numeric($limit)) {
        return ['status'=>0,'msg'=>'请输入正确的条数'];
      }
      $number = ($page - 1) * $limit;
      if (!empty(input('actionid'))) {
          $arr = Db::name('hotel')->where('actionid',input('actionid'))->order('id desc')->limit($number,$limit)->select();
      }else{
          $arr = Db::name('hotel')->order('id desc')->limit($number,$limit)->select();
      }
       foreach ($arr as $k => $v) {
           $action_res = Db::name('action')->field(['title'])->where('id',$v['actionid'])->find();
           if (empty($action_res)) {
               $arr[$k]['action'] = '酒店已被删除';
           }else{
               $arr[$k]['action'] = $action_res['title'];
           }
           $arr[$k]['create_time'] = date('Y-m-d H:i:s',$arr[$k]['create_time']);
           $arr[$k]['update_time'] = date('Y-m-d H:i:s',$arr[$k]['update_time']);
           $arr[$k]['ban_time'] = date('Y-m-d',$arr[$k]['ban_time']);
       }
        if (!empty(input('actionid'))) {
            $count = Db::query("select count(id) from ijiandian_hotel where actionid=".input('actionid'));
        }else{
            $count = Db::query("select count(id) from ijiandian_hotel");
        }
       $count = $count[0]['count(id)'];
       $data['count'] = $count;
       $data['code'] = 0;
       $data['data'] = $arr;
       return $data;
    }
    public function post(){
        $actionid = input('actionid');
        $name = input('name');
        $num = intval(input('num'));
        $price = input('price');
        $head_img = input('head_img');
        $ban_time = input('ban_time');
        if (empty($actionid)) {
            return ['status'=>0,'msg'=>'请选择酒店'];
        }
        if (empty($head_img)) {
            return ['status'=>0,'msg'=>'请上传图片'];
        }
        if (empty($name)) {
            return ['status'=>0,'msg'=>'请输入客房名称'];
        }
        if (!is_numeric($num)) {
            return ['status'=>0,'msg'=>'请输入正确的剩余数量'];
        }
        if (!is_numeric($price)) {
            return ['status'=>0,'msg'=>'请输入正确的价格'];
        }
        if ($num<0) {
            return ['status'=>0,'msg'=>'请输入正确的剩余数量'];
        }
        if ($price<0) {
            return ['status'=>0,'msg'=>'请输入正确的价格'];
        }
        if (!strtotime($ban_time)) {
            return ['status'=>0,'msg'=>'请选择正确的禁止预约时间'];
        }
        $ban_time = strtotime($ban_time);
        $id = input('id');
        if (!empty($id) && is_numeric($id)) {
            $res = Db::name('hotel')->where('id',$id)->update(['actionid'=>$actionid,'name'=>$name,'num'=>$num,'price'=>$price,'update_time'=>time(),'head_img'=>$head_img,'ban_time'=>$ban_time]);
            if ($res == 1) {
                return ['status'=>1,'msg'=>'修改成功'];
            }else{
                return ['status'=>0,'msg'=>'修改失败'];
            }
        }else{
            $res = Db::name('hotel')->insert(['actionid'=>$actionid,'name'=>$name,'num'=>$num,'price'=>$price,'create_time'=>time(),'update_time'=>time(),'head_img'=>$head_img,'ban_time'=>$ban_time]);
            if ($res == 1) {
                return ['status'=>1,'msg'=>'添加成功'];
            }else{
                return ['status'=>0,'msg'=>'添加失败'];
            }
        }
    }
    public function del(){
        $id = input('id');
        if (!empty($id) && is_numeric($id)) {
            $res = Db::name('hotel')->where('id',$id)->delete();
            if ($res == 1) {
                return ['status'=>1,'msg'=>'删除成功'];
            }else{
                return ['status'=>0,'msg'=>'删除失败'];
            }
        }else{
            return ['status'=>0,'msg'=>'id不合法'];
        }
    }
}
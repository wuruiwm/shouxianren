<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2018/12/19
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\model;


use think\Model;
use app\admin\model\Attachment;
use think\Request;
use traits\model\SoftDelete;

class BaseModel extends Model
{
    use SoftDelete;
    protected $field = true;
    protected $hidden = ['update_time', 'delete_time'];

    // 图片url获取器
    public function getThumbAttr($value){

        $img_url = Attachment::where('id',$value)->column('filepath');
        $http = http_type();
        return $http.$img_url[0];
    }

    public function getIconAttr($value){

        $img_url = Attachment::where('id',$value)->column('filepath');
        $http = http_type();
        return $http.$img_url[0];
    }

    /**@method 表格所需返回数据信息
     * @param array $data
     * @param int $code
     * @param string $msg
     * @param int $count
     * @return \think\response\Json
     */
    public static function dataFormat($data=[],$code=0,$msg='',$count=0){

        $request = Request::instance();
        // 当前请求的url
        $url = $request->url(true);
        // 返回信息给客户端
        if($data==null){
            return json([
                'code'=>0,
                'msg'=>'暂无相关数据',
                'count'=>0,
                'data'=>null,
                'requestUrl'=>$url,
            ],200);
        }
        return json([
            'code'=>0,
            'msg'=>'总共有'. $count . '条数据',
            'count'=>$count,
            'data'=>$data,
            'requestUrl'=>$url,
        ]);

    }

    /**
     * @param $data 可查看数据库字段说明进行操作
     * @param $type 操作类型 2积分，3余额
     * @param $sign 1增 2减
     * @return $this
     * $data = [
        'use_action'=>3,
        'type'=>2,
        'amount'=>$number,
        'user'=>$user_id,
        'remark'=>'兑换优惠券',
        ];
     */
    public static function integraOrBalanceLog($data,$type,$sign){
        if($type==2){
            self::startTrans();
            if($sign==1){
                $resultUser = User::where('id',$data['user'])->setInc('integral', $data['amount']);
            }else{
                $resultUser = User::where('id',$data['user'])->setDec('integral', $data['amount']);
            }
            $resultIntegralLog = IntegralLog::createIntegralLog($data);

            if($resultUser && $resultIntegralLog){
                self::commit();
                return true;
            }else{
                self::rollback();
                return false;
            }

        }
        if($type==3){
            self::startTrans();
            if($sign==1){
                $resultUser = User::where('id',$data['user'])->setInc('balance', $data['amount']);
            }else{
                $resultUser = User::where('id',$data['user'])->setDec('balance', $data['amount']);
            }
            $resultBalanceLog = BalanceLog::createBalanceLog($data);
            if($resultUser && $resultBalanceLog){
                self::commit();
                return true;
            }else{
                self::rollback();
                return false;
            }
        }
    }


}
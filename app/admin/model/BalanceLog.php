<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/15
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;

// 余额
class BalanceLog extends BaseModel
{
    public static function createBalanceLog($data)
    {
        return self::create($data);
    }

    public function getUserAttr($value)
    {
        $result = User::where('id', $value)->field(['avatar', 'nickname', 'mobile'], false)->find();
        return $result;
    }


    public static function getList($page, $limit, $key)
    {
        if (isset($key['type']) and !empty($key['type'])) {
            $where['type'] = $key['type'];
        }
        if (isset($key['merchant']) and !empty($key['merchant'])) {
            $where['merchant'] = $key['merchant'];
        }
        if (isset($key['order']) and !empty($key['order'])) {
            $where['order'] = ['like', '%' . $key['order'] . '%'];
        }

        if (isset($key['time']) and !empty($key['time'])) {
            $time = explode(" - ", $key['time']);
            $start_time = GetMkTime($time[0]);
            $end_time = GetMkTime($time[1]);
            $where['create_time'] = ['between', "$start_time,$end_time"];
        }
        if (empty($where['type']) and empty($where['merchant']) and empty($where['create_time']) and empty($where['order'])) {
            $where = null;
        }
        $number = $page * $limit;
        $restult = self::limit($number, $limit)
            ->order('create_time desc')
            ->where($where)
            ->where('status',1)
            ->select();
        if (!$restult) {
            return self::dataFormat(0);
        }
        return self::dataFormat($restult, 0, 'ok', self::count());
    }

    public static function getExportList($type,$time,$order)
    {
        if (!empty($type)) {
            $where['type'] = $type;
        }
        if(!empty($order)){
            $where['order'] = ['like', '%' . $order . '%'];
        }
        if (!empty($time)) {
            $time = explode(" - ", $time);
            $start_time = GetMkTime($time[0]);
            $end_time = GetMkTime($time[1]);
            $where['create_time'] = ['between', "$start_time,$end_time"];
        }
        if ( empty($type) and empty($time) and empty($order) ) {
            $where = null;
        }
        $self = new BalanceLog();
        $result = $self->where($where)
            ->order('create_time desc')
            ->where('status',1)
            ->select();
        $result = self::handleData($result);
        return $result;
    }

    private static function handleData($result)
    {
        foreach ($result as $k => $v) {
            if ($v['use_action'] == 1) {
                $result[$k]['use_action'] = '消费';
            } else if ($v['use_action'] == 2) {
                $result[$k]['use_action'] = '充值';
            } else if ($v['use_action'] == 3) {
                $result[$k]['use_action'] = '兑换';
            } else if ($v['use_action'] == 4) {
                $result[$k]['use_action'] = '返利';
            }

            if ($v['source_type'] == 1) {
                $result[$k]['source_type'] = '微信';
            } else if ($v['source_type'] == 2) {
                $result[$k]['source_type'] = '支付宝';
            } else if ($v['source_type'] == 3) {
                $result[$k]['source_type'] = '后台';
            } else if ($v['source_type'] == 4) {
                $result[$k]['source_type'] = '余额';
            }
            if($v['type']==1){
                $result[$k]['type']='+';
            }else if($v['type']==2){
                $result[$k]['type']='-';
            }else{
                $result[$k]['type']='最终余额';
            }

            if($v['status']==1){
                $result[$k]['status']='已支付';
            }else{
                $result[$k]['status']='待支付';
            }

          
            $result[$k]['user_copy'] = $v['user']['nickname'] . '：' . $v['user']['mobile'];

        }
        return $result;
    }


}
<?php
// +----------------------------------------------------------------------
// | shouxianren_app [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2019~2028 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/20
// +----------------------------------------------------------------------
// | Author: vx:iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\admin\model;


class IntegralLog extends BaseModel
{
    public static function createIntegralLog($data){
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
        if (isset($key['time']) and !empty($key['time'])) {
            $time = explode(" - ", $key['time']);
            $start_time = GetMkTime($time[0]);
            $end_time = GetMkTime($time[1]);
            $where['create_time'] = ['between', "$start_time,$end_time"];
        }
        if (empty($where['type']) and empty($where['merchant']) and empty($where['create_time'])) {
            $where = null;
        }
        $number = $page * $limit;
        $restult = self::limit($number, $limit)
            ->order('create_time desc')
            ->where($where)
            ->select();
        if (!$restult) {
            return self::dataFormat(0);
        }
        return self::dataFormat($restult, 0, 'ok', self::count());
    }

    public static function getExportList($type,$time)
    {
        if (!empty($type)) {
            $where['type'] = $type;
        }
        if (!empty($time)) {
            $time = explode(" - ", $time);
            $start_time = GetMkTime($time[0]);
            $end_time = GetMkTime($time[1]);
            $where['create_time'] = ['between', "$start_time,$end_time"];
        }
        if ( empty($type) and empty($time) ) {
            $where = null;
        }

        $self = new IntegralLog();
        $result = $self->where($where)
            ->order('create_time desc')
            ->select();
        $result = self::handleData($result);
        return $result;
    }

    private static function handleData($result)
    {
        foreach ($result as $k => $v) {
            if ($v['use_action'] == 1) {
                $result[$k]['use_action_copy'] = '签到';
            } else if ($v['use_action'] == 2) {
                $result[$k]['use_action_copy'] = '分享';
            } else if ($v['use_action'] == 3) {
                if($v['type']==3){
                    $result[$k]['use_action_copy'] = '最终积分';
                }else{
                    $result[$k]['use_action_copy'] = '充值';
                }
            } else if ($v['use_action'] == 4) {
                $result[$k]['use_action_copy'] = '消费';
            } else if ($v['use_action'] == 5) {
                $result[$k]['use_action_copy'] = '返还';
            } else if ($v['use_action'] == 6) {
                $result[$k]['use_action_copy'] = '兑换';
            }else{
                $result[$k]['use_action_copy'] = '未知';
            }


            if ($v['source_type'] == 1) {
                $result[$k]['source_type_copy'] = '用户';
            } else if ($v['source_type'] == 2) {
                $result[$k]['source_type_copy'] = '后台';
            }else{
                $result[$k]['source_type_copy'] = '未知';
            }

            if($v['type']==1){
                $result[$k]['type']='+';
            }else if($v['type']==2){
                $result[$k]['type']='-';
            }else{
                $result[$k]['type']='最终积分';
            }

            $result[$k]['user_copy'] = $v['user']['nickname'] . '：' . $v['user']['mobile'];
        }
        return $result;
    }


}
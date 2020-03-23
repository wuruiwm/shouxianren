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


use app\lib\exception\SuccessMessage;
use think\Db;
use think\Exception;


class User extends BaseModel
{
    protected $hidden = ['password', 'pay_pwd'];

    public function getBirthdayAttr($value)
    {
        if ($value == 0) {
            return '';
        }
        return timeToStr($value, 'Y-m-d');
    }

    public function getSexAttr($value)
    {
        return ['', '男', '女'][$value];
    }


    public function getAvatarAttr($value)
    {
        $result = Attachment::where('id', $value)->field(['id', 'filepath'])->find();
        return [
            'id' => $result['id'],
            'filepath' => is_https() . $result['filepath']
        ];
    }

    public static function updateUser($id, $data)
    {

        $self = new User();
        $userInfo = $self->where('id', $id)->find();
        if (isset($data['level_type'])) {
            if ($data['level_type'] == 2) {
                $result = Merchant::withTrashed()->where('user', $id)->find();
                if (!$result) {
                    Merchant::create(['user' => $id, 'img' => $userInfo['avatar']['id']]);
                } else {
                    $user = Merchant::onlyTrashed()->find($result['id']);
                    $result = $user->restore();
                }
            }
            if ($data['level_type'] == 1)
                $result = Merchant::where('user', $id)->find();
            if ($result) {
                Merchant::destroy($result['id']);
            }

        }

        if ($self->allowField(true)->save($data, ['id' => $id]))
            throw new SuccessMessage();
    }

    public static function readUser($page, $limit, $key, $level_type)
    {
        if (isset($key['level_type']) and !empty($key['level_type'])) {
            $where['level_type'] = $key['level_type'];
        }
        if (isset($level_type) and !empty($level_type)) {
            $where['level_type'] = $level_type;
        }
        if (isset($key['mobile']) and !empty($key['mobile'])) {
            $where['mobile'] = ['like', '%' . $key['mobile'] . '%'];
        }

        if (empty($where['level_type']) and empty($level_type) and empty($where['mobile'])) {
            $where = null;
        }

        $number = $page * $limit;
        $result = self::limit($number, $limit)
            ->order('id desc')
            ->where($where)
            ->select();
        if (!$result) {
            return self::dataFormat(0);
        }
        return self::dataFormat($result, 0, 'ok', self::count());
    }

    public static function readUserById($id)
    {
        $result = self::get($id);
        return $result;
    }

    public static function rechargeIntegral($post)
    {
        Db::startTrans();
        try {
                               // exit('1111');
            $result = Rechargeconfig::readRechargeconfig(1);// 充值返利配置
                                
            if ($result['is_support'] == 1) { // 后台充值 返还
                if ($result['integral'] > 0) { // 支持积分返
                    $rebate = $post['amount'] * $result['integral'] / 100;
                }
            }

            if ($post['type_integral'] == 1) { // 增加操作
                if ($result['integral'] > 0) { // 积分返利
                    $amount = $post['amount'] + $rebate;
                    self::where('id', $post['user'])->setInc('integral', $amount);
                    // 记录充值 和 充值返利 记录
                    // 1 记录积分日志
                    $remark = $post['remark'];
                    if(empty($remark)){
                        $remark = '后台充值-增加积分';
                    }
                    $integralData = [
                        'use_action' => 3,
                        'type' => 1,
                        'amount' => $post['amount'],
                        'user' => $post['user'],
                        'source_type' => 2,
                        'remark' => $remark,
                        'admin_nickname' => $post['admin_nickname'],
                        'admin_name' => $post['admin_name'],
                        'admin_id' => $post['admin_id'],
                        'admin_ip' => $post['admin_ip'],

                    ];
                    $res = IntegralLog::create($integralData);
                    if($res){
                        $integralData = [
                            'use_action' => 3,
                            'type' => 1,
                            'amount' => $rebate,
                            'user' => $post['user'],
                            'source_type' => 2,
                            'remark' => '后台充值-增加积分额外返积分',
                            'admin_nickname' => $post['admin_nickname'],
                            'admin_name' => $post['admin_name'],
                            'admin_id' => $post['admin_id'],
                            'admin_ip' => $post['admin_ip'],
                        ];
                        IntegralLog::create($integralData);
                    }

                } else {

                    self::where('id', $post['user'])->setInc('integral', $post['amount']);
                    $remark = $post['remark'];
                    if(empty($remark)){
                        $remark = '后台充值-增加积分';
                    }
                    $integralData = [
                        'use_action' => 3,
                        'type' => 1,
                        'amount' => $post['amount'],
                        'user' => $post['user'],
                        'source_type' => 2,
                        'remark' => $remark,
                        'admin_nickname' => $post['admin_nickname'],
                        'admin_name' => $post['admin_name'],
                        'admin_id' => $post['admin_id'],
                        'admin_ip' => $post['admin_ip'],
                    ];
                    //print_r($integralData);exit();
                    IntegralLog::create($integralData);
                }

            }
            if ($post['type_integral'] == 2){
                self::where('id', $post['user'])->setDec('integral', $post['amount']);
                $remark = $post['remark'];
                if(empty($remark)){
                    $remark = '后台充值-减少积分';
                }
                $integralData = [
                    'use_action' => 3,
                    'type' => 2,
                    'amount' => $post['amount'],
                    'user' => $post['user'],
                    'source_type' => 2,
                    'remark' => $remark,
                    'admin_nickname' => $post['admin_nickname'],
                    'admin_name' => $post['admin_name'],
                    'admin_id' => $post['admin_id'],
                    'admin_ip' => $post['admin_ip'],
                ];
                IntegralLog::create($integralData);
            }

            if ($post['type_integral'] == 3){
                self::where('id', $post['user'])->update(['integral' => $post['amount']]);
                $remark = $post['remark'];
                if(empty($remark)){
                    $remark = '后台充值-最终积分';
                }
                $integralData = [
                    'use_action' => 3,
                    'type' => 3,
                    'amount' => $post['amount'],
                    'user' => $post['user'],
                    'source_type' => 2,
                    'remark' => $remark,
                    'admin_nickname' => $post['admin_nickname'],
                    'admin_name' => $post['admin_name'],
                    'admin_id' => $post['admin_id'],
                    'admin_ip' => $post['admin_ip'],
                ];
                IntegralLog::create($integralData);
            }

            Db::commit();
            return true;
        } catch (Exception $ex) {
            Db::rollback();
            return false;
        }

    }

    public static function rechargeBalance($post)
    {
        Db::startTrans();
        try {
            $result = Rechargeconfig::readRechargeconfig(1);
            if ($result['is_support'] == 1) {
                if ($result['balance'] > 0) {
                    $rebate = $post['amount'] * $result['integral'] / 100;
                }
            }
            if ($post['type_balance'] == 1) {
                $remark = $post['remark'];
                if(empty($remark)){
                    $remark = '后台充值-增加余额';
                }

                if ($result['balance'] > 0) {
                    self::where('id', $post['user'])->setInc('balance', $post['amount']);
                    // 记录余额充值 和 充值余额该返回的积分值
                    // 1 记录余额日志
                    $balanceData = [
                        'use_action' => 2,
                        'type' => 1,
                        'amount' => $post['amount'],
                        'user' => $post['user'],
                        'source_type' => 3,
                        'remark' => $remark,
                        'admin_nickname' => $post['admin_nickname'],
                        'admin_name' => $post['admin_name'],
                        'admin_id' => $post['admin_id'],
                        'admin_ip' => $post['admin_ip'],
                    ];
                    $res = BalanceLog::create($balanceData);
                    if($res){
                        $integralData = [
                            'use_action' => 3,
                            'type' => 1,
                            'amount' => $rebate,
                            'user' => $post['user'],
                            'source_type' => 2,
                            'remark' => '后台充值-增加余额额外返积分',
                            'admin_nickname' => $post['admin_nickname'],
                            'admin_name' => $post['admin_name'],
                            'admin_id' => $post['admin_id'],
                            'admin_ip' => $post['admin_ip'],
                        ];
                        IntegralLog::create($integralData);
                        self::where('id', $post['user'])->setInc('integral', $rebate);

                    }

                } else {
                    self::where('id', $post['user'])->setInc('balance', $post['amount']);
                    $remark = $post['remark'];
                    if(empty($remark)){
                        $remark = '后台充值-增加余额';
                    }
                    $balanceData = [
                        'use_action' => 2,
                        'type' => 1,
                        'amount' => $post['amount'],
                        'user' => $post['user'],
                        'source_type' => 3,
                        'remark' => $remark,
                        'admin_nickname' => $post['admin_nickname'],
                        'admin_name' => $post['admin_name'],
                        'admin_id' => $post['admin_id'],
                        'admin_ip' => $post['admin_ip'],
                    ];
                    BalanceLog::create($balanceData);
                }
            }
            if ($post['type_balance'] == 2){
                self::where('id', $post['user'])->setDec('balance', $post['amount']);
                $remark = $post['remark'];
                if(empty($remark)){
                    $remark = '后台充值-减少余额';
                }
                $balanceData = [
                    'use_action' => 2,
                    'type' => 2,
                    'amount' => $post['amount'],
                    'user' => $post['user'],
                    'source_type' => 3,
                    'remark' => $remark,
                    'admin_nickname' => $post['admin_nickname'],
                    'admin_name' => $post['admin_name'],
                    'admin_id' => $post['admin_id'],
                    'admin_ip' => $post['admin_ip'],
                ];
                BalanceLog::create($balanceData);
            }
            if ($post['type_balance'] == 3){
                self::where('id', $post['user'])->update(['balance' => $post['amount']]);
                $remark = $post['remark'];
                if(empty($remark)){
                    $remark = '后台充值-最终余额';
                }
                $balanceData = [
                    'use_action' => 2,
                    'type' => 3,
                    'amount' => $post['amount'],
                    'user' => $post['user'],
                    'source_type' => 3,
                    'remark' => $remark,
                    'admin_nickname' => $post['admin_nickname'],
                    'admin_name' => $post['admin_name'],
                    'admin_id' => $post['admin_id'],
                    'admin_ip' => $post['admin_ip'],
                ];
                BalanceLog::create($balanceData);
            }
            Db::commit();
            return true;
        } catch (Exception $ex) {
            Db::rollback();
            return false;
        }

    }


}
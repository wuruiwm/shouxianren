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

namespace app\admin\controller;

use app\admin\model\Admin;
use app\admin\model\Rechargeconfig;
use app\admin\model\User as UserModel;
use app\lib\exception\ErrorMessage;
use app\lib\exception\SuccessMessage;
use app\lib\validate\ValidAmount;
use think\Session;

class User extends Permissions
{
    public function index()
    {
        return $this->fetch();
    }

    public function merchant()
    {
        return $this->fetch();
    }

    public function recharge()
    {
        return $this->fetch();
    }

    public function readUser($page = '', $limit = '', $key = '', $level_type = '')
    {
        return UserModel::readUser($page - 1, $limit, $key, $level_type);
    }

    public function updateUser($id)
    {
        return UserModel::updateUser($id, input('post.'));
    }

    public function readUserById($id = '')
    {
        $result = UserModel::readUserById($id);
        if ($result) {
            throw new SuccessMessage([
                'data' => $result
            ]);
        }
        throw new ErrorMessage();
    }

    public function rechargeIntegral()
    {
        (new ValidAmount())->goCheck();
        $post = input('post.');
        $adminInfo = Admin::where('id', Session::get('admin'))->find();
        $post['admin_nickname']=$adminInfo['nickname'];
        $post['admin_name']=$adminInfo['name'];
        $post['admin_id']=$adminInfo['id'];
        $post['admin_ip']=$this->request->ip();
        //print_r($post);exit();
        $result = UserModel::rechargeIntegral($post);
        if ($result) {
            throw new SuccessMessage();
        }
        throw new ErrorMessage(['msg' => '操作失败']);
    }

    public function rechargeBalance()
    {
        (new ValidAmount())->goCheck();
        $post = input('post.');
        $adminInfo = Admin::where('id', Session::get('admin'))->find();
        $post['admin_nickname']=$adminInfo['nickname'];
        $post['admin_name']=$adminInfo['name'];
        $post['admin_id']=$adminInfo['id'];
        $post['admin_ip']=$this->request->ip();
        $result = UserModel::rechargeBalance($post);
        if ($result) {
            throw new SuccessMessage();
        }
        throw new ErrorMessage(['msg' => '操作失败']);
    }

    public function rebateIntegral($amount = 0)
    {
        $result = Rechargeconfig::readRechargeconfig(1);
        if ($result['is_support'] == 1) {
            $amount = $amount * $result['integral'] / 100;
        } else {
            $amount = 0;
        }
        throw new SuccessMessage([
            'data' => ['rebate' => $amount]
        ]);
    }

    public function rebateBalance($amount = 0)
    {
        $result = Rechargeconfig::readRechargeconfig(1);
        if ($result['is_support'] == 1) {
            $amount = $amount * $result['balance'] / 100;
        } else {
            $amount = 0;
        }
        throw new SuccessMessage([
            'data' => ['rebate' => $amount]
        ]);
    }


}
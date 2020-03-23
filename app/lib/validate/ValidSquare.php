<?php
namespace app\lib\validate;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019-05-21
 * Time: 16:01
 */
class ValidSquare extends BaseValidate
{
    protected $rule = [
        'content'=>'require|isNotEmpty',
    ];

    protected $message =[
        'content' => '请输入内容',
    ];
}
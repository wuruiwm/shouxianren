<?php
/*
 * @Author: 傍晚升起的太阳
 * @QQ: 1250201168
 * @Email: wuruiwm@qq.com
 * @Date: 2019-11-21 14:08:32
 * @LastEditors: 傍晚升起的太阳
 * @LastEditTime: 2019-11-22 11:04:48
 */
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://tplay.pengyichen.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------


//api模块公共函数



function getRandomChar($length)
{
    $str = null;
    $strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
    $max = strlen($strPol) - 1;
    for ($i = 0; $i < $length; $i++) {
        $str .= $strPol[rand(0, $max)];
    }
    return $str;
}

function getRandomNumber($length)
{
    return substr(str_shuffle(time()), 0, $length);
}



function array_to_object($arr)
{
    if (gettype($arr) != 'array') {
        return;
    }
    foreach ($arr as $k => $v) {
        if (gettype($v) == 'array' || getType($v) == 'object') {
            $arr[$k] = (object)array_to_object($v);
        }
    }
    return (object)$arr;
}


function object_to_array($obj)
{
    $obj = (array)$obj;
    foreach ($obj as $k => $v) {
        if (gettype($v) == 'resource') {
            return;
        }
        if (gettype($v) == 'object' || gettype($v) == 'array') {
            $obj[$k] = (array)object_to_array($v);
        }
    }
    return $obj;
}


function getSmsCode($mobile)
{
    $code = getRandomNumber(4);
    if (\think\Cache::get($mobile))
        throw new \app\lib\exception\ErrorMessage([
            'msg' => '请再5分钟后重新获取'
        ]);

    $result = \app\lib\alidayu\SendSms::sendSms($mobile, $code);
    $result = object_to_array($result);
    if ($result['Message'] !== 'OK' || $result['Code'] !== 'OK')
        throw new \app\lib\exception\ErrorMessage([
            'msg' => '发送失败',
            'data' => $result
        ]);
    $result = \think\Cache::set($mobile, $code, 300);
    if (!$result)
        throw new \think\Exception('短信缓存异常');
    throw new \app\lib\exception\SuccessMessage();
}


function http_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    if (curl_errno($curl)) return "Curl error：" . curl_error($curl);
    curl_close($curl);
    return $output;
}


function array_insert(&$array, $position, $insert_array)
{
    $first_array = array_splice($array, 0, $position);
    $array = array_merge($first_array, $insert_array, $array);
}






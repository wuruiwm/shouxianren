<?php
// +----------------------------------------------------------------------
// | 寿县人APP [ iActing ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018~2019 http://www.ijiandian.com All rights reserved.
// +----------------------------------------------------------------------
// | Data: 2019/5/19
// +----------------------------------------------------------------------
// | Author: iActing <758246061@qq.com>
// +----------------------------------------------------------------------

namespace app\api\service;


use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use think\Exception;
use think\facade\Cache;
use think\facade\Request;

class Token
{

    public static function generateToken()
    {
        $random = getRandomChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        $salt = config('md5.token_sale');
        return md5($random . $timestamp . $salt);
    }

    public static function getCurrentTokenValue($key = NULL)
    {
        $token = \think\Request::instance()->header('token');
        $cacheValue = \think\Cache::get($token);
        if (!$cacheValue) {
            throw new TokenException();
        } else {
            if (!is_array($cacheValue)) {
                $cacheData = json_decode($cacheValue, true);
                $cache_time = config('cache.token_expire');
                $result_cache = cache($token, $cacheValue, $cache_time);
            }
            if (!$key) {
                return $cacheData;
            }
            if (array_key_exists($key, $cacheData)) {
                return $cacheData[$key];
            } else {
                throw new Exception('无效参数：尝试获取的' . $key . '变量不存在！');
            }
        }
    }
  
    public static function getCurrentTokenValueAlone($key = NULL)
    {
        $token = \think\Request::instance()->header('token');
        $cacheValue = \think\Cache::get($token);
        if (!$cacheValue) {
            return false;
        } else {
            if (!is_array($cacheValue)) {
                $cacheData = json_decode($cacheValue, true);
                $cache_time = config('cache.token_expire');
                $result_cache = cache($token, $cacheValue, $cache_time);
            }
            if (!$key) {
                return $cacheData;
            }
            if (array_key_exists($key, $cacheData)) {
                return $cacheData[$key];
            } else {
                return false;
            }
        }
    }

    public static function getCurrentTokenUserId()
    {
        return self::getCurrentTokenValue('user_id');
    }
  
    public static function getCurrentTokenUserIdAlone()
    {
        return self::getCurrentTokenValueAlone('user_id');
    }

    public static function getCurrentTokenUser()
    {
        return self::getCurrentTokenValue();
    }

    public static function usersOrAdminScope()
    {
        $scope = self::getCurrentTokenValue('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::USER) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    public static function onlyUserScope()
    {
        $scope = self::getCurrentTokenValue('scope');
        if ($scope) {
            if ($scope == ScopeEnum::USER) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    public static function onlyAdminScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::ADMIN) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }
}
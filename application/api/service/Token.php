<?php

/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\service;

use app\lib\enum\ScopeEnum;
use app\lib\exception\ForbiddenException;
use app\lib\exception\ParamException;
use app\lib\exception\TokenException;
use think\Cache;
use Think\Exception;
use think\Request;

class Token
{
    /**
     * Decription :生成令牌
     * return string
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public static function generateToken()
    {
        //随机字符串
        $randChar = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //盐
        $tokenSalt = config('secure.token_salt');

        return md5($randChar . $timestamp . $tokenSalt);
    }

    /**
     * Decription :验证token 是否存在
     * @param $token
     * return bool
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    public static function verifyToken($token)
    {
        $exist = Cache::get($token);
        if ($exist) {
            return true;
        } else {
            return false;
        }
    }

    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    /**
     * Decription : 传入指定的key 返回需要的信息
     * @param $key
     * return mixed
     * @throws Exception
     * @throws TokenException
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
            if (array_key_exists($key, $vars)) {
                return $vars[$key];
            } else {
                throw new Exception('Token变量不存在');
            }

        }
    }

    //验证token是否合法或者是否过期
    //验证器验证只是token验证的一种方式
    //另外一种方式是使用行为拦截token，根本不让非法token
    //进入控制器
    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope >= ScopeEnum::User) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    /**
     * Decription :获取管理员权限
     * return bool
     * @throws Exception
     * @throws ForbiddenException
     * @throws TokenException
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    public static function checkSuperScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if ($scope) {
            if ($scope == ScopeEnum::Super) {
                return true;
            } else {
                throw new ForbiddenException();
            }
        } else {
            throw new TokenException();
        }
    }

    /**
     * Decription :获取当前用户的uid
     * return mixed
     * @throws Exception
     * @throws ParamException
     * @throws TokenException
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        $scope = self::getCurrentTokenVar('scope');
        if($scope == ScopeEnum::Super){
            // 只有Super权限才可以自己传入uid
            // 且必须在get参数中，post不接受任何uid字段
            $userID = input('get.uid');
            if(!$userID){
                throw new ParamException([
                    'msg'=>'没有指定需要挫折的用户对象'
                ]);
            }
            return $userID;
        }else{
            return $uid;
        }

    }

    /**
     * Decription :检查传入的uid 是否合法
     * @param $checkedUID
     * return bool
     * @throws Exception
     * @throws ParamException
     * @throws TokenException
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public static function isValidOperate($checkedUID)
    {
        if(!$checkedUID){
            throw new Exception('检查UID时必须传入一个被检查的UID');
        }
        $currentOperateUID = self::getCurrentUid();
        if($currentOperateUID == $checkedUID){
            return true;
        }
        return false;
    }

}
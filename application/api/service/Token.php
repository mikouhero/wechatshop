<?php

/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */
namespace app\api\service;

use think\Cache;

class Token
{
    /**
     * Decription :生成令牌
     * return string
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public  static function generateToken()
    {
        //随机字符串
        $randChar = getRandChar(32);
        $timestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //盐
        $tokenSalt = config('secure.token_salt');

        return md5($randChar.$timestamp.$tokenSalt);
    }

    public  static function verifyToken($token)
    {
        $exist = Cache::get($token);
        if($exist){
            return true;
        }else{
            return false;
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\service;

use app\api\model\User;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;
use app\lib\exception\WeChatException;
use think\Exception;

class UserToken extends Token
{
    protected $code;
    protected $wxLoginUrl;
    protected $wxAppID;
    protected $wxAppSecret;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'), $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    /**
     * 登陆
     * 思路1：每次调用登录接口都去微信刷新一次session_key，生成新的Token，不删除久的Token
     * 思路2：检查Token有没有过期，没有过期则直接返回当前Token
     * 思路3：重新去微信刷新session_key并删除当前Token，返回新的Token
     */
    public function get()
    {
        // 拿到微信的返回结果
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new Exception('获取session_key及openID时异常，微信内部错误');
        } else {

            $loginFail = array_key_exists('errcode', $wxResult);
            if ($loginFail) {
                //错误异常封装后易于扩展
                $this->processLoginError($wxResult);
            } else {
                return $this->grantToken($wxResult);
            }
        }
    }

    /**
     * Decription :微信登录异常处理
     * @param $wxResult
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    private function processLoginError($wxResult)
    {
        throw new WeChatException(
            [
                'msg' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]
        );
    }

    /**
     * Decription :
     * @param $wxResult  微信返回的成功信息
     * return string     加密后的token
     * @throws TokenException
     * @author: Mikou.hu
     * Date: 2018/10/29\
     */
    private function grantToken($wxResult)
    {
        $openid = $wxResult['openid'];
        $user = User::getByOpenID($openid);
        if (!$user) {
            $uid = $this->newUser($openid);
        } else {
            $uid = $user->id;
        }
        //写入缓存
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    /**
     * Decription : 将用户的信息存入缓存 key
     * @param $wxRsult
     * return string
     * @throws TokenException
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    private function saveToCache($wxRsult)
    {
        $key = self::generateToken();
        $value = json_encode($wxRsult);
        $expire_in = config('setting.token_expire_in');
        $result = cache($key,$value,$expire_in);
        if(!$result){
            throw new TokenException([
                'msg'=>'服务器缓存异常',
                'errorCode' =>10005
            ]);
        }
        return $key;
    }

    /**
     * Decription : 组装用户的信息
     * @param $wxResult
     * @param $uid
     * return mixed
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    private function prepareCachedValue($wxResult, $uid)
    {
        $cacheValue = $wxResult;
        $cacheValue['uid'] = $uid;
        // 作用域
        $cacheValue['scope'] = ScopeEnum::User;
        return $cacheValue;
    }

    /**
     * Decription : 添加一个新用户
     * @param $openid
     * return mixed
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    private function newUser($openid)
    {
        $user = User::create(['openid' => $openid]);
        return $user->id;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */
namespace app\api\service;

class UserToken  extends Token
{
    protected $code ;
    protected $wxLoginUrl ;
    protected $wxAppID ;
    protected $wxAppSecret ;

    function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID= config('wx.app_id');
        $this->wxAppSecretconfig('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\lib\exception;

/**
 * Class WechatException
 * @package app\lib\exception
 */
class WechatException extends BaseException
{
    public $code = 400;
    public $msg = "wechat unknow error";
    public $errorCode = 999;
}
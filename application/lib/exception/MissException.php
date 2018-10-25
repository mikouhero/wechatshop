<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\lib\exception;


class MissException extends BaseException
{
    public  $code = 404;
    public  $msg = "请求的资源不存在";
    public $errorCode = 10001;
}
<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/1
 */

namespace app\lib\exception;


class OrderException extends  BaseException
{
    public $code = 404;
    public $msg = '订单不存在，请检查ID';
    public $errorCode = 80000;
}
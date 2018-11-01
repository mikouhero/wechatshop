<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/1
 */

namespace app\lib\exception;


class UserException extends BaseException
{
    public $code = 404;
    public $msg = '用户不存在';
    public $errorCode = 60000;
}
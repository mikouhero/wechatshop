<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    public $code = 403;
    public $msg = '权限不够';
    public $errorCode = 10001;
}
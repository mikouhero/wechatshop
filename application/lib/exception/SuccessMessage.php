<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/25
 */

namespace app\lib\exception;


class SuccessMessage extends BaseException
{
    public $code = 200;
    public $msg = 'ok';
    public $errorCode = 0;


}
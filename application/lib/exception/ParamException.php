<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\lib\exception;

/**
 * Class ParamException
 * @package app\lib\exception
 * 通用参数错误
 */
class ParamException extends BaseException
{
    public $code = 400;
    public $errorCode = 10000;
    public $msg = "参数无效";

}
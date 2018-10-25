<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */
namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
    public $code = 400;
    public $msg = "参数无效";
    public $errorCode = 999;
    public $shouldToClient = true;

    /**
     * BaseException constructor.
     * @param array $param
     * 关联数组只含 code msg  errorCode
     */
    public function __construct($param = [])
    {
        if (!is_array($param)) {
            return;
        }
        if (array_key_exists('code', $param)) {
            $this->code = $param['code'];
        }

        if (array_key_exists('msg', $param)) {
            $this->msg = $param['msg'];
        }

        if (array_key_exists('errorCode', $param)) {
            $this->errorCode = $param['errorCode'];
        }
    }
}
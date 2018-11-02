<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/2
 */

namespace app\lib\exception;


class CategoryException extends BaseException
{
    public $code = 404;
    public $msg = '指定类目不存在，请检查商品ID';
    public $errorCode = 20000;
}
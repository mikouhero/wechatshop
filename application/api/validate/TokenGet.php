<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\validate;


class TokenGet extends BaseValidate
{
    protected $rule = [
        'code' => 'require|isNotEmpty'
    ];

    protected $message = [
        'code' => 'token都没有你想干啥'
    ];
}
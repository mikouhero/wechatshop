<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\validate;


class Count extends BaseValidate
{
    protected $rule =[
        'count'=>'isPositiveInteger|between:1,15',
    ];
}
<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/25
 */

namespace app\api\validate;


class ThemeProduct extends BaseValidate
{
    protected $rule = [
        't_id' => 'member',
        'p_id' => 'member'
    ];
}
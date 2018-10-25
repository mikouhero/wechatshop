<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */
namespace  app\api\validate;
class IDMustBePositiveInt extends BaseValidate
{
    protected $rule = [
      'id' => 'require|isPositiveInteger'
    ];
}
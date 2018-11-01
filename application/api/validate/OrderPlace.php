<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\api\validate;

use app\lib\exception\ParamException;

class OrderPlace extends BaseValidate
{
//$products = ['product_id'=>1,'count'=>2 ]
    protected $rule = [
        'products' => 'checkProducts'
    ];
    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];
    protected function checkProducts($values)
    {
        if (empty($values)) {
            throw new ParamException([
                'msg' => '商品列表不能为空'
            ]);
        }
        foreach ($values as $value) {
            $this->checkProduct($value);
        }
        return true;
    }

    private function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if (!$result) {
            throw new ParamException([
                'msg' => '商品列表参数错误',
            ]);
        }
        return true;
    }
}
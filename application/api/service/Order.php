<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\api\service;


class Order
{
    //下单商品列表
    protected $oProducts;
    //真实商品数据
    protected $products;
    protected $uid;

    function __construct()
    {
    }

    public function place($uid,$oProducts)
    {
        $this->oProducts= $oProducts;
    }

}
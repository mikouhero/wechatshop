<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\api\service;


use app\api\model\Product;

class Order
{
    //下单商品列表
    protected $oProducts;
    //真实商品数据
    protected $products;
    //用户uid
    protected $uid;

    function __construct()
    {
    }

    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->uid = $uid;
        $this->products = $this->getProductsByOrder($oProducts);
        $status = $this->getOrderStatus();
    }

    /**
     * Decription :通过下单数据返回数据库中的详细信息
     * @param $oProducrs
     * return mixed
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    private function getProductsByOrder($oProducrs)
    {
        //下单商品id 集合
        $oPID = [];
        foreach ($oProducrs as $v) {
            array_push($oPID, $v['product_id']);
        }
        $products = Product::all($oPID)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
        return $products;

    }

    private function getOrderStatus()
    {
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            // 保存订单里面所有商品的详细信息历史订单
            'pStatusArray' => []
        ];

        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct['product_id'], $oProduct['count'], $this->products);
        }
    }

    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = 1;
        $pStatus = [
            'id' => null,
            // 是否有库存
            'hasStock' => false,
            //数量
            'count' => 0,
            'name' => '',
            //总价
            'totalPrice' => 0
        ];
        for ($i = 0; $i < count($products); $i++) {

        }
    }
}
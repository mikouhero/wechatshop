<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\OrderPlace;
use app\api\service\Token;
use app\api\service\Order as OrderService;
use think\Request;

class Order extends BaseController
{
    public function placeOrder()
    {
        //json 格式数据
 //       {"products":	[{"product_id":1,"count":1}]}

        (new OrderPlace())->goCheck();

        $products = input('post.products/a');
        $uid = Token::getCurrentUid();
        $order = new OrderService();
        $status = $order->place($uid,$products);
        return $status;
    }
}
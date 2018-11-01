<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\OrderPlace;
use app\api\service\Token;
use app\api\service\Order as OrderService;
use app\api\model\Order as OrderModel;
use app\api\validate\PagingParameter;
use app\lib\exception\OrderException;
use app\lib\exception\SuccessMessage;

class Order extends BaseController
{
    /**
     * Decription : 下订单接口
     * return array
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
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

    /**
     * Decription : 获取订单详情
     * @param $id
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public function getDetail($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $orderDetail = OrderModel::get($id);
        if(!$orderDetail){
            throw  new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }

    /**
     * Decription :通过用户id 分页获取订单列表
     * @param int $page
     * @param int $size
     * return array
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public function getSummaryByUser($page=1,$size=15)
    {
        (new PagingParameter()) ->goCheck();
        $uid = Token::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid,$page,$size);
        if($pagingOrders->isEmpty()){
            return  [
                'current_page'=>$pagingOrders->currentPage(),
                'data' =>[]
            ];
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    /**全部订单简要信息（分页）
     * Decription :
     * @param int $page
     * @param int $size
     * return array
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public function getSummary($page=1,$size=20)
    {
        (new PagingParameter())->goCheck();
        $pagingOrders = OrderModel::getSummaryByPage($page, $size);
        if ($pagingOrders->isEmpty())
        {
            return [
                'current_page' => $pagingOrders->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingOrders->hidden(['snap_items', 'snap_address'])
            ->toArray();
        return [
            'current_page' => $pagingOrders->currentPage(),
            'data' => $data
        ];
    }

    /**
     * Decription :发货
     * @param $id
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public function delivery($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $order = new OrderService();
        $success = $order->delivery($id);
        if($success){
            return new SuccessMessage();
        }



    }


}
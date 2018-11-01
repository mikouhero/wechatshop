<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\api\service;


use app\api\model\OrderProduct;
use app\api\model\Product;
use app\api\model\UserAddress;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\UserException;
use think\Exception;
use app\api\model\Order as OrderModel;

class Order
{
    //下单商品列表
    protected $oProducts;
    //数据库真实商品数据
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
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        // 通过检测  创建订单
        $orderSnap = $this->snapOrder();
        $status = self::createOrderByTrans($orderSnap);
        $status['pass'] = true;
        return $status;
    }

    /**
     * Decription :发货接口
     * @param $orderID
     * @param string $jumpPage
     * @throws OrderException
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public function delivery($orderID, $jumpPage = '')
    {
        $order = OrderModel::where('id', '=', $orderID)->find();
        if (!$order) {
            throw new OrderException();
        }
        if ($order->status != OrderStatusEnum::PAID) {
            throw new OrderException([
                'msg' => '还没付款呢，想干嘛？或者你已经更新过订单了，不要再刷了',
                'errorCode' => 80002,
                'code' => 403
            ]);
        }
        $order->status = OrderStatusEnum::DELIVERED;
        $order->save();
        $message = new DeliveryMessage();
        return $message->sendDeliveryMessage($order,$jumpPage);
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

    /**
     * Decription :获取订单的状态以及订单数据和总价
     * return array
     * @throws OrderException
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
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
            if (!$pStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] += $pStatus['totalPrice'];
            array_push($status['pStatusArray'], $pStatus);
        }

        return $status;
    }

    /**
     * Decription : 通过下单单个商品id 返回商品的总价 是否有库存 name id 信息
     * @param $oPID
     * @param $oCount
     * @param $products
     * return array
     * @throws OrderException
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    private function getProductStatus($oPID, $oCount, $products)
    {
        $pIndex = -1;
        $pStatus = [
            'id' => null,
            // 是否有库存
            'haveStock' => false,
            //数量
            'count' => 0,
            'name' => '',
            //总价
            'totalPrice' => 0
        ];
        for ($i = 0; $i < count($products); $i++) {
            //遍历真实商品，判断是否有该订单商品的id
            if ($oPID == $products[$i]['id']) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            // product_id 不存在
            throw new OrderException([
                'msg' => 'id为' . $oPID . '的商品不存在，订单创建失败'
            ]);
        } else {
            //有该商品获取商品信息
            $product = $products[$pIndex];
            $pStatus['id'] = $product['id'];
            $pStatus['name'] = $product['name'];
            $pStatus['count'] = $oCount;
            $pStatus['totalPrice'] = $product['price'] * $oCount;
            if ($product['stock'] - $oCount >= 0) {
                $pStatus['haveStock'] = true;
            }
        }
        return $pStatus;
    }

    /**
     * Decription : 生成所有商品的订单快照
     * return array
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    private function snapOrder()
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => json_encode($this->getUserAddress()),
            'snapName' => $this->products[0]['name'],
            'snapImg' => $this->products[0]['main_img_url'],
        ];
        if (count($this->products) > 1) {
            $snap['snapName'] .= '等';
        }

        for ($i = 0; $i < count($this->products); $i++) {
            $product = $this->products[$i];
            $oProduct = $this->oProducts[$i];
            $pStatus = $this->snapProduct($product, $oProduct['count']);
            $snap['orderPrice'] += $pStatus['totalPrice'];
            $snap['totalCount'] += $pStatus['count'];
            array_push($snap['pStatus'], $pStatus);
        }

        return $snap;
    }

    /**
     * Decription :获取用户下单时的地址
     * return array
     * @throws UserException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id', '=', $this->uid)->find();
        if (!$userAddress) {
            throw  new UserException(
                [
                    'msg' => '用户收货地址不存在，下单失败',
                    'errorCode' => 60001,
                ]
            );
        }
        return $userAddress->toArray();
    }

    /**
     * Decription : 生成单个商品的快照信息
     * @param $product
     * @param $oCount
     * return array
     * @author: Mikou.hu
     * Date: 2018/11/1\
     */
    private function snapProduct($product, $oCount)
    {
        $pStatus = [
            'id' => null,
            'name' => null,
            'main_img_url' => null,
            'count' => $oCount,
            'totalPrice' => 0,
            'price' => 0
        ];
        $pStatus['count'] = $oCount;
        $pStatus['totalPrice'] = $oCount * $product['price'];
        $pStatus['name'] = $product['name'];
        $pStatus['id'] = $product['id'];
        $pStatus['main_img_url'] = $product['main_img_url'];
        $pStatus['price'] = $product['price'];
        return $pStatus;
    }

    /**
     * Decription : 简化处理  创建订单时没有预扣除库存量，简化处理  如果预扣除了库存量需要队列支持，且需要使用锁机制
     * @param $snap
     * return array
     * @throws Exception
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    private function createOrderByTrans($snap)
    {
        try {
            $orderNo = $this->makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snap['orderPrice'];
            $order->total_count = $snap['totalCount'];
            $order->snap_img = $snap['snapImg'];
            $order->snap_name = $snap['snapName'];
            $order->snap_address = $snap['snapAddress'];
            $order->snap_items = json_encode($snap['pStatus']);
            $order->save();

            $orderID = $order->id;
            $create_time = $order->create_time;

            foreach ($this->oProducts as &$p) {
                $p['order_id'] = $orderID;
            }
            $orderProduct = new OrderProduct();
            $orderProduct->saveAll($this->oProducts);
            $orderProduct->saveAll($this->oProducts);
            return [
                'order_no' => $orderNo,
                'order_id' => $orderID,
                'create_time' => $create_time
            ];
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Decription :创建订单号
     * return string
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public static function makeOrderNo()
    {
        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
        $orderSn =
            $yCode[intval(date('Y')) - 2018] . strtoupper(dechex(date('m'))) . date(
                'd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf(
                '%02d', rand(0, 99));
        return $orderSn;
    }
}
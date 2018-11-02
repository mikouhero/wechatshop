<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/2
 */

namespace app\api\service;

use app\api\model\Order;
use app\api\model\Product;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;
use think\Exception;
use think\Loader;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{
//    protected $data = <<<EOD
//<xml><appid><![CDATA[wxaaf1c852597e365b]]></appid>
//<bank_type><![CDATA[CFT]]></bank_type>
//<cash_fee><![CDATA[1]]></cash_fee>
//<fee_type><![CDATA[CNY]]></fee_type>
//<is_subscribe><![CDATA[N]]></is_subscribe>
//<mch_id><![CDATA[1392378802]]></mch_id>
//<nonce_str><![CDATA[k66j676kzd3tqq2sr3023ogeqrg4np9z]]></nonce_str>
//<openid><![CDATA[ojID50G-cjUsFMJ0PjgDXt9iqoOo]]></openid>
//<out_trade_no><![CDATA[A301089188132321]]></out_trade_no>
//<result_code><![CDATA[SUCCESS]]></result_code>
//<return_code><![CDATA[SUCCESS]]></return_code>
//<sign><![CDATA[944E2F9AF80204201177B91CEADD5AEC]]></sign>
//<time_end><![CDATA[20170301030852]]></time_end>
//<total_fee>1</total_fee>
//<trade_type><![CDATA[JSAPI]]></trade_type>
//<transaction_id><![CDATA[4004312001201703011727741547]]></transaction_id>
//</xml>
//EOD;

    /**
     * Decription :  检测库存量（万一超卖？怎么解决）  更新订单status状态   减库存    成功 返回成功消息 ，失败 返回没有成功
     * 回调方法入口，子类可重写该方法
     * 注意：
     * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
     * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
     * 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
     * @param $data
     * @param $msg
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == "SUCCESS") {
            $orderNo = $data['out_trade_no'];
            DB::startTrans();
            try {
                $order = Order::where('order_no', '=', $orderNo)->lock(true)->find();
//                status =1 为订单未处理状态
                if ($order->status == 1) {
                    $service = new OrderService();
//                    判断库存量
                    $status = $service->checkOrderStock($order->id);
                    if ($status['pass']) {
                        $this->updateOrderStatus($order->id, true);
                        $this->reduceStock($status);
                    } else {
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            } catch (Exception $ex) {
                Db::rollback();
                log::error($ex);
                return false;
            }
        }
        return true;
    }

    /**
     * Decription :根据订单号减少库存量
     * @param $status
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    private function reduceStock($status)
    {
        foreach ($status['pStatusArray'] as $singlePStatus) {
            Product::where('id', '=', $singlePStatus['id'])
                ->setDec('stock', $singlePStatus['count']);
        }
    }

    /**
     * Decription : 更新订单状态 已支付或者已支付但是没库存
     * @param $orderID
     * @param $success
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    private function updateOrderStatus($orderID, $success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        Order::where('id', '=', $orderID)->update(['stattus' => $status]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/1
 */

namespace app\api\service;


use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Exception;
use think\Loader;
use app\api\model\Order as OrderModel;
use think\Log;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class Pay
{
    private $orderNo;
    private $orderID;

    function __construct($orderID)
    {
        if (!$orderID) {
            throw new  Exception('订单号不允许为 NULL');
        }
        $this->orderID = $orderID;
    }

    public function pay()
    {
        $this->checkOrderValid();
        $order = new Order();
        $status = $order->checkOrderStock($this->orderID);
        if (!$status['pass']) {
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    /**
     * Decription :微信支付订单信息
     * @param $totalPrice
     * return array
     * @throws TokenException
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    private function makeWxPreOrder($totalPrice)
    {
        $openid = Token::getCurrentTokenVar('openid');
        if (!$openid) {
            throw new TokenException();
        }
        //没有命名空间时，记得加反斜杠
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee($totalPrice * 100);
        $wxOrderData->SetBody('凯尔亮商贸');
        $wxOrderData->SetOpenid($openid);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }

    /**
     * Decription :向微信请求订单号并生成签名
     * @param $wxOrderData
     * return array
     * @throws Exception
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    private function getPaySignature($wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != "SUCCESS" || $wxOrder['result_code'] != 'SUCCESS') {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败', 'error');
            throw new Exception('获取预支付订单失败');
        }
        // 保存到数据库 发送模板消息
        $this->recordPreOrder($wxOrder);
        $signature = $this->sign($wxOrder);
        return $signature;
    }

    /**
     * Decription :更新数据库的prepay_id
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    private function recordPreOrder($wxOrder)
    {
        // 必须是update 每次用户取消支付后再次对同一订单支付，prepay_id是不同的
        OrderModel::where('id', '=', $this->orderID)->update(['prepay_id' => $wxOrder['prepay_id']]);
    }

    /**
     * Decription :微信签名
     * @param $wxOrder
     * return array
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time() . mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->SetPackage('prepay_id=' . $wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $rawValues = $jsApiPayData->GetValues();
        $rawValues['paySign'] = $sign;
//        appid返回客户端并没有实际作用
        unset($rawValues['appId']);
        return $rawValues;
    }

    /**
     * Decription : 安全性原则，客户端数据全部不可信，尤其涉及到钱这一字眼
     * 订单号可能不存在，和用户不匹配，已经支付过
     * @throws OrderException
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    private function checkOrderValid()
    {
        $order = OrderModel::where('id', '=', $this->orderID)->find();
        if (!$order) {
            throw new OrderException();
        }
        if (!Token::isValidOperate($order->user_id)) {
            throw new TokenException(
                [
                    'msg' => '订单与用户不匹配',
                    'errorCode' => 10003
                ]);
        }
        if ($order->status != OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'msg' => '订单已支付过啦',
                'errorCode' => 80003,
                'code' => 400
            ]);
        }
        $this->orderNo = $order->order_no;
        return true;
    }
}
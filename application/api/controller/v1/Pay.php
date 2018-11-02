<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/1
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\WxNotify;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Pay as PayService;

class Pay extends BaseController
{
    /**
     * Decription :微信发起预订单请求
     * @param $id
     * return array
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    public function getPreOrder($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $pay = new PayService($id);
        return $pay->pay();
    }

    /**
     * Decription :路由转发
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    public function redirectNotify()
    {
        $notify = new WxNotify();
        $notify->handle();
    }

    /**
     * Decription :路由转发
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    public function notifyConcurrency()
    {
        $notify = new WxNotify();
        $notify->handle();
    }

    /**
     * Decription :接收微信回调
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    public function receiveNotify()
    {
        //        获取微信返回的xml数据
//         $xmlData = file_get_contents('php://input');
//         Log::error($xmlData);
//      	 error_log($xmlData,'fuck.log');
//	     error_log($_REQUEST,'test.log');
        //检测库存量，超卖，更新status
        $notify = new WxNotify();
        $notify->handle();
        //    做一次转发，然后就可以进行断点调试了
//        $xmlData = file_get_contents('php://input');
//        $result = curl_post_raw('http:/zerg.cn/api/v1/pay/re_notify?XDEBUG_SESSION_START=13133',
//            $xmlData);
//        return $result;
//        Log::error($xmlData);
    }
}
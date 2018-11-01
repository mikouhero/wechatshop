<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/1
 */

namespace app\api\service;


use think\Exception;

class WxMessage
{

    private $sendUrl = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?" .
    "access_token=%s";
    private $touser;
    //不让子类控制颜色
    private $color = 'black';

    protected $tplID;
    protected $page;
    protected $formID;
    protected $data;
    protected $emphasisKeyWord;

    function __construct()
    {
        $accessToken = new AccessToken();
        $token = $accessToken->get();
        $this->sendUrl = sprintf($this->sendUrl,$token);
    }

    /**
     * Decription :发送消息
     * @param $openID
     * return bool
     * @throws Exception
     * @author: Mikou.hu
     * Date: 2018/11/1
     */
    public function sendMessage($openID)
    {
        $data = [
            'touser' => $openID,
            'template_id' => $this->tplID,
            'page' => $this->page,
            'form_id' => $this->formID,
            'data' => $this->data,
//            'color' => $this->color,
            'emphasis_keyword' => $this->emphasisKeyWord
        ];
        $res = https_request($this->sendUrl,$data);
        $res = json_decode($res,true);
        if($res['errcode'] == 0){
            return true;
        }else{
            throw new Exception('模板消息发送失败 ，'.$res['errmsg']);
        }
    }

}
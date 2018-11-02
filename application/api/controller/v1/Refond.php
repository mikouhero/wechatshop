<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/2
 */

namespace app\api\controller\v1;



use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\service\Refund as RefundService;

class Refond extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiveScope' => ['only' => 'getPreOrder']
    ];

    public function getPreRefund($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $refund = new RefundService;
        return $refund->refund();
    }
}
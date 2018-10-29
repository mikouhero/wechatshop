<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\service\Token;
use app\api\model\User;
use app\api\model\UserAddress;
use app\api\validate\AddressNew;
use app\lib\exception\SuccessMessage;
use app\lib\exception\UserException;

class Address extends BaseController
{
    protected $beforeActionList = [
        'checkPrimaryScope' => ['only' => 'createOrUpdateAddress,getUserAddress']
    ];

    /**
     * Decription 获取用户的地址
     * return array|false|mixed|\PDOStatement|string|\think\Model
     * @throws UserException
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    public function getUserAddress()
    {
        $uid = Token::getCurrentUid();
        $userAddress = UserAddress::where('user_id', '=', $uid)->find();
        if (!$userAddress) {
            throw new UserException([
                'msg' => '用户地址不存在',
                'errorCode' => 60001
            ]);
        }
        return $userAddress;
    }

    /**
     * Decription :更新或者创建用户地址
     * return SuccessMessage
     * @throws UserException
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    public function createOrUpdateAddress()
    {
        $validate = new AddressNew();
        $validate->goCheck();
        // 根据token 获取uid
        $uid = Token::getCurrentUid();
        $user = User::get($uid);
        $data = $validate->getDataByRule(input('post.'));
        if (!$user) {
            throw new UserException([
                'code' => 404,
                'msg' => '不存在的用户',
                'errorCode' => 60001
            ]);
        }
        $userAddress = $user->address;
        if (!$userAddress) {
            $user->Address()->save();
        } else {
            $user->address->save($data);
        }

        return new SuccessMessage();
    }
}
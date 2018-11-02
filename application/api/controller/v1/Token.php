<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\controller\v1;


use app\api\service\UserToken;
use app\api\service\AppToken;
use app\api\validate\TokenGet;
use app\api\validate\AppTokenGet;
use app\lib\exception\ParamException;
use app\api\service\Token as TokenService;

class Token
{
    /**
     * Decription :通过code换取token
     * @url-post /api/token/user
     * {'code' :123}
     * @param string $code
     * return array
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    public function getToken($code = '')
    {
        (new TokenGet())->goCheck();
        $wx = new UserToken($code);
        $token = $wx->get();
        return [
            'token' => $token
        ];
    }

    /**
     * Decription :检验token是否过期
     * @url-post /api/v1/token/verify
     * {token:"123"}
     * @param string $token
     * return array
     * @throws ParamException
     * @author: Mikou.hu
     * Date: 2018/10/29
     */
    public function verifyToken($token = '')
    {
        if (!$token) {
            throw new ParamException([
                'msg' => 'token不允许为空'
            ]);
        }
        $valid = TokenService::verifyToken($token);

        return ['isValid' => $valid];
    }

    public function getAppToken($ac = '', $se = '')
    {
        //解决跨域问题
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header('Access-Control-Allow-Methods: GET');
        (new AppTokenGet())->goCheck();
        $app = new AppToken();
        $token = $app->get($ac, $se);
        return [
            'token' => $token
        ];
    }
}
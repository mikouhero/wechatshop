<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/29
 */

namespace app\api\model;


class User extends BaseModel
{

    public static function getByOpenID($openid)
    {
        $user = User::where('openid', '=', $openid)->find();
        return $user;
    }

    public function address()
    {
        return $this->hasOne('UserAddress','user_id','id');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\api\controller\v1;

use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\MissException;

class Banner
{
    public  function getBanner($id)
    {
       $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        $banner = BannerModel :: getBannerById($id);
        if(!$banner){
            throw new MissException([
                'msg' => '请求的banner不存在',
                'errorCode' => 40000
            ]);
        }
        return json($banner);
    }
}
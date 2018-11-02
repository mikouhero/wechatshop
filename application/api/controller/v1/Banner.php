<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\api\controller\v1;

use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\MissException;

class Banner extends BaseController
{
    /**
     * Decription :通过id 获取指定的bannerl列表
     * @url-get api/v1/banner/1
     * @param $id
     * return \think\response\Json
     * @throws MissException
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
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
<?php

/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\api\model;


class Banner extends BaseModel
{
    public function items()
    {
        //                           关联的模型名            外键                本模型的主键
        return  $this->hasMany('BannerItem','banner_id','id');

    }
    public static function getBannerById($id)
    {                     // 关联模型的方法
        $banner = self::with(['items','items.img'])->find($id);
        return $banner;
    }
}
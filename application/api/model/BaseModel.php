<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    protected $hidden = ['delete_time', 'from', 'update_time'];

    protected function prefixImgUrl($value,$data)
    {
        $finalUrl = $value;
        if($data['from'] == 1){
            $finalUrl = config('setting.img_prefix').$value;
        }
        return $finalUrl;
    }
}
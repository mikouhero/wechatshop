<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\api\model;


use think\Model;

class BannerItem extends Model
{
    protected $hidden = ['img_id', 'banner_id', 'delete_time'];

    public function img()
    {
        return $this->belongsTo('Image','img_id','id');
    }
}
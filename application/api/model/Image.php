<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\api\model;


class Image extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'from','id'];

    // 读取器 自动执行
    public function getUrlAttr($value,$data)
    {
        return $this->prefixImgUrl($value,$data);
    }
}
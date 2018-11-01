<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/11/1
 */

namespace app\api\model;


class Order extends BaseModel
{
    protected $hidden = [ 'delete_time', 'update_time'];

    protected $autoWriteTimestamp = true;

    public function getSnapItemsAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }


    public function getSnapAddressAttr($value)
    {
        if(empty($value)){
            return null;
        }
        return json_decode($value);
    }


    public  function products()
    {
        return $this->belongsToMany('Product','order_product','product_id','order_id');
    }

    public static function getSummaryByuser($uid,$page=1,$size=15)
    {
        $pageingData = self::where('user_id','=',$uid)
                        ->order('create_time desc')
                        ->paginate($size,true,['page'=>$page]);
        return $pageingData;
    }


    public static function getSummaryByPage($page=1, $size=20){
        $pagingData = self::order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
        return $pagingData ;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\model;

class Category extends BaseModel
{

    public function products()
    {
        return $this->hasMany('Product','category_id','id');
    }

    public function img()
    {
        return $this->belongsTo('Image','topic_img_id','id');
    }

    public  static  function getCategories()
    {
        $categories = self::with('products')->with('products.imgs')->select();
        return $categories;
    }
    /**
     * Decription : 指定id 下的商品
     * @param $id
     * return array|false|mixed|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public static function getCategory($id)
    {
        $category = self::with('products')
            ->with('products.imgs')
            ->find($id);
        return $category;
    }
}
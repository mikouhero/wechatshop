<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/25
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $autoWriteTimestamp = 'datetime';
    //    pivot 时多对多关系表
    protected $hidden = [
        'delete_time', 'main_img_id', 'pivot', 'from', 'category_id',
        'create_time', 'update_time'];

    /**
     * Decription :图片属性
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public function imgs()
    {
        return $this->hasMany('ProductImage','product_id','id');
    }

    public function getMainImgUrlAttr($value,$data)
    {
        return $this->prefixImgUrl($value,$data);
    }

    /**
     * Decription :商品属性
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public  function  properties()
    {
        return $this->hasMany('ProductProperty','product_id','id');
    }

    /**
     * Decription : 获取指定分类下的商品
     * @param $categoryID
     * @param bool $paginate
     * @param int $page
     * @param int $size
     * return false|mixed|\PDOStatement|string|\think\Collection|\think\Paginator
     * @throws \think\exception\DbException
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public static function getProductsByCategoryID($categoryID, $paginate = true, $page = 1, $size = 30)
    {
        $query = self::where('category_id','=',$categoryID);
        if(!$paginate){
            return $query->select();
        }else{
            // paginate  第二参数true表示采用简洁模式，简洁模式不需要查询记录总数
            return $query->paginate(
                $size,true,['page'=>$page]
            );
        }
    }


    /**
     * Decription :获取最近的商品
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public static function getMostRecent($count)
    {
        $products = self::limit($count)
            ->order('create_time desc')
            ->select();
        return $products;
    }

    /**
     * Decription : 获取制定商品的详细信息
     * @param $id
     * return array|false|mixed|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public static function getProductDetail($id)
    {
        $producrt = self::with(['imgs'=>function($query)
                {
                    // 链式排序
                    $query->with('imgUrl')->order('order','asc');
                }
        ])->with('properties')->find($id);
        return $producrt;
    }
}
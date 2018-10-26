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
}
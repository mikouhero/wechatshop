<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\ProductException;
use app\lib\exception\ThemeException;
use think\Controller;
use app\api\model\Product as ProductModel;
use app\api\validate\Count;

class Product extends Controller
{
    public function getByCategory($id=-1,$page=1,$size=30)
    {
        (new IDMustBePositiveInt())->goCheck();
    }

    /**
     * Decription :获取某分类下全部商品(不分页）
     * @url /product/by_category/:id
     * @param int $id
     * return false|mixed|\PDOStatement|string|\think\Collection|\think\Paginator
     * @throws ThemeException
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public function getAllInCategory($id = -1)
    {
        (new IDMustBePositiveInt())->goCheck();
        $products = ProductModel::getProductsByCategoryID($id,false);
        if($products->isEmpty()){
            throw  new ThemeException();
        }
        return $products;
    }

    /**
     * Decription 获取最近的商品
     * @url product/recent/:count
     * @param int $count
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public  function getRecent($count=15)
    {
        (new Count())->goCheck();
        $products = ProductModel::getMostRecent($count);
        if($products->isEmpty()){
            throw ProductException();
        }
         $products= $products->hidden(['summary']);
        return $products;
    }
}
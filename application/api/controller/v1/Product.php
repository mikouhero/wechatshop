<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\controller\v1;


use app\api\validate\IDMustBePositiveInt;
use app\api\validate\PagingParameter;
use app\lib\exception\ProductException;
use app\lib\exception\ThemeException;
use think\Controller;
use app\api\model\Product as ProductModel;
use app\api\validate\Count;

class Product extends Controller
{
    protected $beforeActionList = [
        'checkSuperScope' => ['only' => 'createOne,deleteOne']
    ];

    /**
     * Decription :获取指定分类下的商品 分页
     * @param int $id
     * @param int $page
     * @param int $size
     * return array
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public function getByCategory($id=-1,$page=1,$size=30)
    {
        (new IDMustBePositiveInt())->goCheck();
        (new PagingParameter())->goCheck();
        $pagingProducts = ProductModel::getProductsByCategoryID($id,true,$page,$size);
        if($pagingProducts->isEmpty()){
            // 对于分页最好不要抛出MissException，客户端并不好处理
            return [
                'current_page' => $pagingProducts->currentPage(),
                'data' => []
            ];
        }
        $data = $pagingProducts
            ->hidden(['summary'])
            ->toArray();
        return [
            'current_page' => $pagingProducts->currentPage(),
            'data' => $data
        ];
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

    /**
     * Decription : 获取指定id 的商品
     * @param $id
     * return ProductModel|array|false|mixed|null|\PDOStatement|string|\think\Model
     * @throws ProductException
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public function getOne($id)
    {
        (new IDMustBePositiveInt()) ->goCheck();
        $product = ProductModel::getProductDetail($id);
        if(!$product){
            throw new ProductException();
        }
        return $product;
    }

}
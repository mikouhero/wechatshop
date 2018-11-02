<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/26
 */

namespace app\api\controller\v1;


use app\api\controller\BaseController;
use app\api\validate\IDMustBePositiveInt;
use app\lib\exception\MissException;
use app\api\model\Category as CategoryModel;

class Category extends BaseController
{
    /**
     * Decription :获取所有分类
     * @url-get /api/category/all
     * return false|static[]
     * @throws MissException
     * @throws \think\exception\DbException
     * @author: Mikou.hu
     * Date: 2018/10/26
     */
    public function getAllCategories()
    {
        $categories = CategoryModel::all([],'img');
        if(empty($categories)){
            throw new MissException([
                'msg'=>'无任何分类',
                'errorCode'=>50000
            ]);
        }
        return $categories;
    }


    /**
     * Decription :获取指定分类下的商品
     * @url-get /api/category/2
     * @param $id
     * return array|false|\PDOStatement|string|\think\Model
     * @throws MissException
     * @author: Mikou.hu
     * Date: 2018/11/2
     */
    public function  getCategory($id)
    {
        $validate = new IDMustBePositiveInt();
        $validate->goCheck();
        $category = CategoryModel::getCategory($id);
        if(empty($category)){
            throw new MissException([
                'msg'=>'404 NOT FOUND',
            ]);
        }

        return $category;
    }


}
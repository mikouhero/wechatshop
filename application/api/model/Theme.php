<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/25
 */

namespace app\api\model;


use app\lib\exception\ProductException;
use app\lib\exception\ThemeException;


class Theme extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'topic_img_id', 'head_img_id'];

    /**
     * Decription :
     * return \think\model\relation\BelongsTo
     * @author: Mikou.hu
     * Date: 2018/10/25
     * 关联image
     * 带外键的表一般定义为belongsTo 另一方使用hasOne
     */
    public function topicImg()
    {
        //        一对一 hasone(和belongsto反向，当没有外键时使用)
        return $this->belongsTo('Image', 'topic_img_id', 'id');

    }

    public function headImg()
    {
        return $this->belongsTo('Image', 'head_img_id', 'id');
    }

    /**
     * Decription :关联多对多关系
     * @author: Mikou.hu
     * Date: 2018/10/25
     */
    public function products()
    {                                   // 关联模型名          //中间表              // 关联模型外键        //     当前模型关联键
        return $this->belongsToMany('Product', 'theme_product', 'product_id', 'theme_id');
    }

    public static function getThreeWithProduct($id)
    {
        $themes = self::with('products,topicImg,headImg')->select($id);

        return $themes;
    }

    public function addProductTheme($themeID, $productID)
    {
        $models = self::checkRelationExist($themeID, $productID);
        // 写入中间表 即使中间表已存在相同的themeId 和itemid 的数据 写入不成功 但是tp不会报错
        // 最好插入前做检查
        $models['theme']->products()->attach($productID);
        return true;
    }

    public function deleteThemeProduct($themeID, $productID)
    {
        $models = self::checkRelationExist($themeID, $productID);
        $models['theme']->products()->detach($productID);
        return true;

    }

    private static function checkRelationExist($themeID, $productID)
    {
        $theme = self::get($themeID);
        if (!$theme) {
            throw new ThemeException();
        }

        $product = Product::get($productID);
        if (!$product) {
            throw new ProductException();
        }

        return [
            'theme' => $theme,
            'product' => $product
        ];
    }
}
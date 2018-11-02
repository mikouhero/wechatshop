<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/25
 */

namespace app\api\controller\v1;


use app\api\validate\IDCollection;
use app\api\validate\IDMustBePositiveInt;
use app\api\validate\ThemeProduct;
use app\lib\exception\SuccessMessage;
use app\lib\exception\ThemeException;
use think\Controller;
use app\api\model\Theme as ThemeModel;

class Theme extends Controller
{
    /**
     * Decription : 获取主题列表
     * @param string $ids
     * @url-get     /api/v1/theme?ids=1
     * @return  array of theme
     * @throws ThemeException
     * 实体查询分单一和列表查询，可以只设计一个接收列表接口，
     *       单一查询也需要传入一个元素的数组
     *       对于传递多个数组的id可以选用post传递、
     *       多个id+分隔符或者将多个id序列化成json并在query中传递
     * @author: Mikou.hu
     * Date: 2018/10/25
     */
    public function getSimpleList($ids = '')
    {
        $validate = new IDCollection();
        $validate->goCheck();
        $ids = explode(',', $ids);
        //一组用find  多组用select
        //修改database 文件 resultset_type 改为 collection
        $result = ThemeModel::with('topicImg,headImg')->select($ids);
        if ($result->isEmpty()) {
            throw new ThemeException();
        }
        return $result;

    }

    /**
     * Decription : 获取指定主题下的描述以及 products
     * @url-get /theme/getComplexOne/2
     * @param $id
     * return mixed
     * @throws ThemeException
     * @author: Mikou.hu
     * Date: 2018/10/25
     */
    public function getComplexOne($id)
    {
        (new IDMustBePositiveInt())->goCheck();
        $theme = ThemeModel::getThreeWithProduct($id);
        if ($theme->isEmpty()) {
            throw new ThemeException();
        }

        return $theme->hidden(['products.summary'])->toArray();

    }

    /**
     * Decription :添加主题
     * @url /theme/:t_id/product/:p_id
     * @param $t_id
     * @param $p_id
     * return SuccessMessage
     * @author: Mikou.hu
     * Date: 2018/10/25
     */
    public function addThemeProduct($t_id, $p_id)
    {
        $validate = new ThemeProduct();
        $validate->goCheck();
        ThemeModel::addThemeProduct($t_id, $p_id);
        return new SuccessMessage();
    }

    public function deleteThemeProduct($t_id, $p_id)
    {
        $validate = new ThemeProduct();
        $validate->goCheck();
        $themeID = (int)$t_id;
        $productID = (int)$p_id;
        ThemeModel::deleteThemeProduct($themeID, $productID);
        return new SuccessMessage();

    }
}
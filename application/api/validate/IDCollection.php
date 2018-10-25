<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/25
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    /**
     * @var array
     *不要在require | chechIDs中间添加空格
     * 不然会不执行
     */
    protected $rule = [
        'ids' => 'require|checkIDs'
    ];
    protected $message = [
        'ids' => 'ids参数必须为以逗号分隔的多个正整数'
    ];

    protected function checkIDs($value)
    {
            $value = explode(',',$value);
            if(empty($value)){
                return false;
            }
            foreach ($value as $id)
            {
                if(!$this->isPositiveInteger($id)){
                    // id必须是正整数
                    return false;
                }
            }
            return true;
    }
}
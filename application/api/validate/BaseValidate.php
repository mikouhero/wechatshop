<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/24
 */

namespace app\api\validate;


use app\lib\exception\ParamException;
use think\Request;
use think\Validate;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        $request = Request::instance();
        $param = $request->param();
        $param['token'] = $request->header('token');
        if(!$this->check($param)){
            $exception = new ParamException([
                 'msg' => is_array($this->error) ? implode(';',$this->error) : $this->error
            ]);
            throw $exception;
        }

        return true;
    }

    protected function isPositiveInteger($value,$rule='',$data='',$field='')
    {
        if(is_numeric($value) && is_int($value+0) && ($value + 0) >0 ) {
            return true;
        }
//        return false;
        return $field . '必须是正整数';

    }
}
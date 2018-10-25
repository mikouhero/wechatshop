<?php
/**
 * Created by PhpStorm.
 * User: Mikou.hu
 * Date: 2018/10/25
 */

namespace app\api\model;


class Theme extends BaseModel
{
    protected $hidden = ['delete_time','update_time','topic_img_id','head_img_id'];

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
        return $this->belongsTo('Image','topic_img_id','id');

    }

    public  function  headImg()
    {
        return $this->belongsTo('Image','head_img_id','id');
    }
}
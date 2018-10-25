<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;
//获取banner信息
Route::get('api/:v/banner/:id','api/:v.Banner/getBanner');

//获取主题信息

Route::group('api/:version/theme',function(){
//    避免提前匹配
    Route::get('', 'api/:version.Theme/getSimpleList');
});